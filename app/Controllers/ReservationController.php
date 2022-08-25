<?php

namespace App\Controllers;

use App\Entities\Appointment;
use App\Auth\Auth;
use App\Entities\Location;
use App\Exceptions\ValidationException;
use App\Session\Flash;
use App\Views\View;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Laminas\Diactoros\Response;
use League\Route\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReservationController extends Controller
{
    public function __construct(protected View $view, protected EntityManager $db, protected Auth $auth, protected Router $router, protected Flash $flash)
    {
    }

    /**
     * @throws \Exception
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $insertedDate = $request->getQueryParams()['selectedDateForm'];
        $appointments = $this->db->getRepository(Appointment::class)->matching(
            Criteria::create()->where(Criteria::expr()->eq('date', new \DateTime($insertedDate)))
        )->getValues();
        $locations = $this->db->getRepository(Location::class)->findAll();
        return $this->view->render(new Response, '/home.twig', ['appointment' => $locations]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function store(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->validateAppointment($request);
        } catch (\Exception $exception) {
            dd($exception);
        }

        if ($this->validateDate($data, $request)) {
            $this->createAppointment($data);
        }
        return redirect($this->router->getNamedRoute('home')->getPath());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function createAppointment(array $data): ResponseInterface
    {
        $appointment = new Appointment();
        $locations = $this->db->getRepository(Location::class)->find($data['location']);
        $reservationDate = \DateTime::createFromFormat('Y-m-d', $data['date']);


        $appointment->fill([
            'date' => $reservationDate, //->format('Y-m-d'),
            'location' => $locations,
            'user' => $this->auth->user(),
        ]);
        $this->db->persist($appointment);
        $this->db->flush();
        return new Response\JsonResponse(true);
    }

    function getAppointments(ServerRequestInterface $request): ResponseInterface
    {
        $appointments = $this->db->getRepository(Appointment::class)->findBy([
            'date' => \DateTime::createFromFormat('Y-m-d', $request->getParsedBody()['date']),
        ]);
        $preparedAppointments = [];
        foreach ($appointments as $appointment) {
            $preparedAppointments[] = [
                'name' => $appointment->user->name,
                'location_name' => $appointment->location->city,
                'location_address' => $appointment->location->address,
            ];
        }
        return new Response\JsonResponse($preparedAppointments);
    }

    private function validateDate(array $data, ServerRequestInterface $request): bool
    {
        $appointmentByLoggedInUser = $this->db->getRepository(Appointment::class)->count(['user' => $this->auth->user(),
            'date' => \DateTime::createFromFormat('Y-m-d', $request->getParsedBody()['date']),
        ]);
        if ($appointmentByLoggedInUser > 0 || (date('Y-m-d') <= \DateTime::createFromFormat('Y-m-d', $request->getParsedBody()['date']))) {
            $this->flash->now('error', 'Select a future day');
            return false;
        }
//            $this->flash->now('error','You already have an appointment on this day ');
//            return false;
//        }
        return true;
    }

    /**
     * @throws ValidationException
     */
    private
    function validateAppointment(ServerRequestInterface $request): array
    {
        return $this->validate($request, ['location' => ['required'], 'date' => ['required']]);

    }

}
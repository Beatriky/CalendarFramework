<?php

namespace App\Controllers;

use App\Entities\Appointment;
use App\Auth\Auth;
use App\Entities\Location;
use App\Exceptions\ValidationException;
use App\Views\View;
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
    public function __construct(protected View $view, protected EntityManager $db, protected Auth $auth, protected Router $router)
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

        // dd($appointments[0]->getUser());

        return $this->view->render(new Response, '/home.twig', ['appointment' => $locations]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws ValidationException
     */
    public function store(ServerRequestInterface $request): ResponseInterface
    {
        // dd("asd");
        try {
            $data = $this->validateAppointment($request);

        } catch (\Exception $exception) {
            dd($exception);
        }
        $this->createAppointment($data);
        return redirect($this->router->getNamedRoute('home')->getPath());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function createAppointment(array $data): Appointment
    {
        $appointment = new Appointment();
        $locations = $this->db->getRepository(Location::class)->find($data['location']);
        $reservationDate = \DateTime::createFromFormat('Y-m-d', $data['date']);
        $appointment->fill([
            'date' => $reservationDate->format('Y-m-d'),
            'location' => $locations,
            'user' => $this->auth->user(),
        ]);

        $this->db->persist($appointment);
        $this->db->flush();
        return $appointment;
    }

    /**
     * @throws ValidationException
     */
    private function validateAppointment(ServerRequestInterface $request): array
    {
        return $this->validate($request, ['location' => ['required'], 'date' => ['required']]);
    }
}
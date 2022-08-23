<?php

namespace App\Controllers;

use App\app\Entities\Appointment;
use App\Auth\Auth;
use App\Entities\Location;
use App\Views\View;
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

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $locations = $this->db->getRepository(Location::class)->findAll();
        return $this->view->render(new Response, '/home.twig', ['locations' => $locations]);
        // ($request->getQueryParams());
    }

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->validateAppointment($request);
        } catch (Exception $ex) {
            dd($ex);
        }

        dd($data);
        $this->createAppointment($data);
        //return redirect($this->router->getNamedRoute('reservation')->getPath());
        return redirect($this->router->getNamedRoute('home')->getPath());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function createAppointment(array $data): Appointment
    {
        $appointment = new Appointment();
        $locations = $this->db->getRepository(Location::class)->findAll();
        $reservationDate = \DateTime::createFromFormat('Y-m-d', $data['date']);

        $appointment->fill([
            'appointment' => $reservationDate,
            'locations' => $locations[$data['locations']],
            'user' => $this->auth->user()
        ]);
        $this->db->persist($appointment);
        $this->db->flush();
        return $appointment;
    }

    private function validateAppointment(ServerRequestInterface $request): array
    {
        return $this->validate($request, ['locations' => ['required'], 'date' => ['required']]);
    }
}
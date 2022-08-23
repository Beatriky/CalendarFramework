<?php

namespace App\app\Controllers;

use App\app\Entities\Appointment;
use App\Entities\Location;
use App\Views\View;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AppointmentController
{
    public function __construct(protected View $view, protected EntityManager $db)
    {

    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $location = $this->db->getRepository(Location::class)->findAll();

        return $this->view->render(new Response, 'appointment.twig', ['locations' => $location
        ]);
    }
    public function store()
    {

    }
    public function createReservation($selectedDate)
    {
        $appointment = new Appointment();
        $location = $this->db->getRepository(Location::class)->findAll();
        //   dd($location);
        $selectedDate = \DateTime::createFromFormat('d-m-y', $selectedDate['date']);


    }

    private function validateAppointment(ServerRequestInterface $request): array
    {
        return $this->validate($request,
            ['date' => ['required'],
                'locations' => ['required']]);
    }
}
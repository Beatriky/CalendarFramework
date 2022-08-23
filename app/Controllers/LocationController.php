<?php

namespace App\app\Controllers;

use App\app\Entities\Appointment;
use App\Entities\Location;
use App\Views\View;
use Doctrine\ORM\EntityManager;

class LocationController
{
    public function __construct(protected View $view, protected EntityManager $db)
    {

    }

    public function setLocation(): void
    {
        $query="select * from locations";
    }

//$appointment->fill(['location' => $location[$selectedDate['location']]], 'user'=>$this->auth->user(), 'appointment_date'=>$selectedDate);
}
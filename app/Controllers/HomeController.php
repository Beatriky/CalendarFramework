<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Location;
use App\Session\Flash;
use App\Views\View;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    public function __construct(protected View $view, protected EntityManager $db,protected Flash $flash)
    {
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {

        $getParams = $request->getQueryParams();
        if (array_key_exists("date", $getParams)) {
            $date = $getParams['date'];
            // $locations = $this->db->getRepository(Location::class)->matching(Criteria::create()->where(Criteria::expr()->eq('date', \Datetime::createFromFormat('Y-m-d', $date))))->getValues();
        }
        $locations = $this->db->getRepository(Location::class)->findAll();
        return $this->view->render(new Response, 'home.twig', ['locations' => $locations]);
    }
}
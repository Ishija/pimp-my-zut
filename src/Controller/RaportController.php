<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Raport;
use Symfony\Component\Routing\Annotation\Route;

class RaportController extends AbstractController
{
    private $raport;

    public function __construct(Raport $raport)
    {
        $this->raport = $raport;
    }

    public function index(): Response
    {
        return new Response('Raport has been generated');
    }
}

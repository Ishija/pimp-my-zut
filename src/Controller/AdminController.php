<?php

namespace App\Controller;

use App\Repository\MeetingsRepository;
use App\Repository\ProfessorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(MeetingsRepository $meetingsRepository): Response
    {
        $meetings = $meetingsRepository->findAll();
        return $this->render('admin/index.html.twig', [
            "meetings" => $meetings
        ]);
    }
    #[Route('/admin/download', name: 'app_admin_download')]
    public function donwload_excel(MeetingsRepository $meetingsRepository): Response
    {
        //funkcja generowania pliku
        return new Response();

    }
}

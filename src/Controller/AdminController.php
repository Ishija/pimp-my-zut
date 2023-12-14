<?php

namespace App\Controller;

use App\Repository\MeetingsRepository;
use App\Repository\ProfessorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Raport;

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
    #[Route('/admin/{meeting_id}', name: 'app_admin_download')]
    public function download_excel(MeetingsRepository $meetingsRepository, int $meeting_id): Response
    {
        $raport = new Raport($meetingsRepository);
        $raport->generateReport($meeting_id);

        return new Response();
    }
}

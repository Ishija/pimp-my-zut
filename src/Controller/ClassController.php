<?php

namespace App\Controller;

use App\ZutEduAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassController extends AbstractController
{
    #Generating QRs: https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={LINK}

    #[Route('/class/{classId}')] # id = class id (for example: WI WI1- 316)
    public function show(string $classId) : Response
    {
        $api = new ZutEduAPI();
        $data = $api->getClassData($classId);

        $currentClass = $data[1]; // assume it's sorted

        $lecturer = $currentClass["worker"];
        $subject = $currentClass["subject"];

        echo $classId;
        echo "<BR>";
        echo $lecturer;
        echo "<BR>";
        echo $subject;

        return new Response();
    }
}

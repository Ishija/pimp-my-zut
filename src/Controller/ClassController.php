<?php

namespace App\Controller;

use App\Service\ZutEduAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class ClassController extends AbstractController
{
    #Generating QRs: https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={LINK}

    private const NO_CLASSES = "Nie udało się znaleźć zajęć :(";

    #[Route('/class/{classId}')] # classId = class id (for example: WI WI1- 316)
    public function show(string $classId, #[MapQueryParameter] string $now = 'now') : Response
    {
        $api = new ZutEduAPI();
        $data = $api->getClassData($classId, new \DateTime($now));

        if (count($data) == 1) {
            echo self::NO_CLASSES;
            return new Response();
        }

        $currentClass = $this->getCurrentClass($data);

        if (empty($currentClass)) {
            echo self::NO_CLASSES;
            return new Response();
        }

        $lecturer = $currentClass["worker"];
        $subject = $currentClass["subject"];

        echo $classId;
        echo "<BR>";
        echo $lecturer;
        echo "<BR>";
        echo $subject;

        return new Response();
    }

    private function getCurrentClass(array $data) : array {
        $now = ZutEduAPI::getNow();

        foreach($data as $d) {
            if (!array_key_exists("title", $d)) {
                continue;
            }

            $start = new \DateTime($d["start"]);
            $end = new \DateTime($d["end"]);

            if ($start->format("Y-m-d H:i:s") < $now->format("Y-m-d H:i:s") &&
                $end->format("Y-m-d H:i:s") > $now->format("Y-m-d H:i:s")) {
                return $d;
            }
        }

        return [];
    }
}

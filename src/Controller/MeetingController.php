<?php

namespace App\Controller;

use App\Service\ZutEduAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class MeetingController extends AbstractController
{
    #Generating QRs: https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={LINK}

    private const NO_MEETINGS = "Nie udało się znaleźć zajęć :(";

    #[Route('/meeting/{meetingId}')] # meetingId = meeting id (for example: WI WI1- 316)
    public function show(string $meetingId, #[MapQueryParameter] string $now = 'now') : Response
    {
        $api = new ZutEduAPI();
        $data = $api->getMeetingData($meetingId, new \DateTime($now));

        if (count($data) == 1) {
            echo self::NO_MEETINGS;
            return new Response();
        }

        $currentClass = $this->getCurrentMeeting($data);

        if (empty($currentClass)) {
            echo self::NO_MEETINGS;
            return new Response();
        }

        $lecturer = $currentClass["worker"];
        $subject = $currentClass["subject"];

        echo $meetingId;
        echo "<BR>";
        echo $lecturer;
        echo "<BR>";
        echo $subject;

        return new Response();
    }

    private function getCurrentMeeting(array $data) : array {
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

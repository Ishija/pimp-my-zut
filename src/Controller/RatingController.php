<?php

namespace App\Controller;

use App\Service\ZutEduAPI;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RatingController extends AbstractController
{
    public function __construct(private LoggerInterface $logger){}

    #[Route('/rating/{roomId}')] # roomId = room (for example: WI WI1- 316)
    public function getLecture(string $roomId, #[MapQueryParameter] string $now = 'now'): Response
    {
        $api = new ZutEduAPI();
        $data = $api->getMeetingData($roomId, new \DateTime($now));

        if (count($data) == 1) {
            return $this->render("error.html.twig", ['message' => "Brak sali/przedmiotu do oceny"]);
        }

        $currentClass = $this->getCurrentMeeting($data, new \DateTime($now));

        if (empty($currentClass)) {
            return $this->render("error.html.twig", ['message' => "Brak sali/przedmiotu do oceny"]);
        }

        $lecture = new LectureData(
            $currentClass["title"],
            $roomId,
            $currentClass["worker"],
            new \DateTime($currentClass["start"]),
            new \DateTime($currentClass["end"])
        );

        return $this->render("ratingPage.html.twig", [
            'id' => 0, // TODO ?
            'name' => $lecture->name,
            'lecturer' => $lecture->lecturer,
            'room' => $lecture->room,
            'start' => $lecture->getStart(),
            'end' => $lecture->getEnd()
        ]);
    }

    #[Route('/add')]
    public function addRate(Request $request) : Response{
        $id = $request->request->get("lecture-id");
        $rate = $request->request->get("emoji-rate");
        $opinion = $request->request->get("rate-opinion");

        if($id == null){
            return $this->render("error.html.twig", ['message' => "Brak id wykładu"]);
        }

        if($rate == null){
            return $this->render("error.html.twig", ['message' => "Nie podano oceny"]);
        }

        $rateData = new RateData($id, $rate, $opinion); // TODO
        
        return $this->render("thanksForRate.html.twig");
    }

    private function getCurrentMeeting(array $data, $now) : array {
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

class RateData{
    public $lectureId;
    public $rating;
    public $opinion;

    public function __construct(int $lectureId, int $rating, ?string $opinion){
        $this->$lectureId = $lectureId;
        $this->$rating = $rating;
        $this->$opinion = $opinion;
    }
}

class LectureData{
    public $name;
    public $room;
    public $lecturer;
    private \DateTime $start;
    private \DateTime $end;

    public function __construct(string $name, string $room, string $lecturer, \DateTime $start, \DateTime $end){
        $this->name = $name;
        $this->room = $room;
        $this->lecturer = $lecturer;
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): string
    {
        return $this->start->format("H:i");
    }

    public function getEnd(): string
    {
        return $this->end->format("H:i");
    }
}
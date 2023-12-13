<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class RatingController extends AbstractController
{
    public function __construct(private LoggerInterface $logger){}

    #[Route('/rating/{id}')]
    public function getLecture(int $id): Response
    {
        if($id == 0){ // ok
            $lecture = new LectureData(235, "Testowanie Oprogramowania", "WI2-120", "Jan Nowak", 1702368900, 1702374300);

            return $this->render("ratingPage.html.twig", [
                'id' => $lecture->id,
                'name' => $lecture->name,
                'lecturer' => $lecture->lecturer,
                'room' => $lecture->room,
                'start' => $lecture->getStart(),
                'end' => $lecture->getEnd()
            ]);
        }
        else if($id == 1){ // ok
            $lecture = new LectureData(127, "Aplikaje Internetowe", "WI1-302", "Adam Kowalski", 1702203300, 1702208700);

            return $this->render("ratingPage.html.twig", [
                'id' => $lecture->id,
                'name' => $lecture->name,
                'lecturer' => $lecture->lecturer,
                'room' => $lecture->room,
                'start' => $lecture->getStart(),
                'end' => $lecture->getEnd()
            ]);
        }
        else{ // brak przedmiotu do oceny
            return $this->render("error.html.twig", ['message' => "Brak sali/przedmiotu do oceny"]);
        }
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
    public $id;
    public $name;
    public $room;
    public $lecturer;
    private $start;
    private $end;

    public function __construct(int $id, string $name, string $room, string $lecturer, int $start, int $end){
        $this->id = $id;
        $this->name = $name;
        $this->room = $room;
        $this->lecturer = $lecturer;
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): string
    {
        return date("H:i", $this->start);
    }

    public function getEnd(): string
    {
        return date("H:i", $this->end);
    }
}
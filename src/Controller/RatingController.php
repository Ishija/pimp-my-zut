<?php

namespace App\Controller;

use App\Service\ZutEduAPI;
use http\Cookie;
use Psr\Log\LoggerInterface;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\MeetEval;
use App\Entity\Meetings;
use App\Entity\Professor;
use Doctrine\ORM\EntityManagerInterface;
class RatingController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {}

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
            'name' => $lecture->name,
            'lecturer' => $lecture->lecturer,
            'room' => $lecture->room,
            'start' => $lecture->getStart(),
            'end' => $lecture->getEnd()
        ]);
    }

    #[Route('/add')]
    public function addRate(Request $request): Response
    {
        $survey_dl = $request->cookies->get("survey_delay");
        if(new \DateTime($survey_dl) > new \DateTime()){
            return $this->render("error.html.twig", ['message' => "Za szybko!!!"]);
        }

        $rate = $request->request->get("emoji-rate");
        $opinion = $request->request->get("rate-opinion");

        $name = $request->request->get("lecture-name");
        $lecturer = $request->request->get("lecture-teacher");
        $room = $request->request->get("lecture-room");
        $start = $request->request->get("lecture-start");
        $end = $request->request->get("lecture-end");

        if ($rate == null) {
            return $this->render("error.html.twig", ['message' => "Nie podano oceny"]);
        }

        // Convert RateData to MeetEval entity and persist it
        $meetEval = new MeetEval();
        $meetEval->setScore($rate);
        $meetEval->setInfo($opinion);
        $meetEval->setCreationTime(new \DateTime());

        // Check if the meeting exists
        $meeting = $this->entityManager->getRepository(Meetings::class)->findOneBy(['meeting_room' => $room, 'meeting_start' => new \DateTime($start)]);

        if (!$meeting) {
            $meeting = new Meetings();
            $meeting->setMeetingRoom($room);
            $meeting->setMeetingName($name);
            $meeting->setMeetingStart(new \DateTime($start));
            $meeting->setMeetingEnd(new \DateTime($end));

            // Check if the professor exists
            $professor = $this->entityManager->getRepository(Professor::class)->findOneBy(['teacher' => $lecturer]); // Replace 'Teacher' with the actual teacher name

            if (!$professor) {
                // If the professor does not exist, create a new one
                $professor = new Professor();
                $professor->setTeacher($lecturer); // Set the professor name accordingly
                $professor->generateEmail(); // Set the professor email accordingly
                $professor->setTotalScore(100);
            }

            $professor->setTotalScore($professor->getTotalScore() + $rate);

            $meeting->setProf($professor);
        }

        $meeting->addMeetEval($meetEval);

        // Update total score of the professor
        $professor = $meeting->getProf();
        $professor->setTotalScore($professor->getTotalScore() + $rate);

        $this->entityManager->persist($meetEval);
        $this->entityManager->persist($meeting);
        $this->entityManager->persist($professor);
        $this->entityManager->flush();

        $contents = $this->renderView("thanksForRate.html.twig");
        $response= new Response($contents);
        $response->headers->setCookie(new \Symfony\Component\HttpFoundation\Cookie("survey_delay",(new \DateTime("+15 minutes"))->format("Y-m-d H:i:s")));
        return $response;
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
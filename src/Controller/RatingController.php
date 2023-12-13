<?php

namespace App\Controller;

use App\Service\ZutEduAPI;
use Psr\Log\LoggerInterface;
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
            'id' => 0, // TODO ?
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
        $id = $request->request->get("lecture-id");
        $rate = $request->request->get("emoji-rate");
        $opinion = $request->request->get("rate-opinion");

        if ($id == null) {
            return $this->render("error.html.twig", ['message' => "Brak id wykładu"]);
        }

        if ($rate == null) {
            return $this->render("error.html.twig", ['message' => "Nie podano oceny"]);
        }

        // Convert RateData to MeetEval entity and persist it
        $meetEval = new MeetEval();
        $meetEval->setScore($rate);
        $meetEval->setInfo($opinion);
        $meetEval->setCreationTime(new \DateTime());

        // Check if the meeting exists
        $meeting = $this->entityManager->getRepository(Meetings::class)->find($id);

        if (!$meeting) {
            // If the meeting does not exist, create a new one
            $meeting = new Meetings();
            $meeting->setMeetingRoom("Room"); // Set the meeting room accordingly
            $meeting->setMeetingName("Meeting"); // Set the meeting name accordingly
            $meeting->setMeetingStart(new \DateTime());
            $meeting->setMeetingEnd(new \DateTime());

            // Check if the professor exists
            $professor = $this->entityManager->getRepository(Professor::class)->findOneBy(['teacher' => 'Teacher']); // Replace 'Teacher' with the actual teacher name

            if (!$professor) {
                // If the professor does not exist, create a new one
                $professor = new Professor();
                $professor->setTeacher("Teacher"); // Set the professor name accordingly
                $professor->setEmail("teacher@example.com"); // Set the professor email accordingly
                $professor->setTotalScore(0);
            }

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
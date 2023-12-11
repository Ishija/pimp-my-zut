<?php

namespace App\Entity;

use App\Repository\MeetingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingsRepository::class)]
class Meetings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $prof_id = null;

    #[ORM\Column(length: 255)]
    private ?string $meeting_room = null;

    #[ORM\Column(length: 255)]
    private ?string $meeting_name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $meeting_start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $meeting_end = null;

    #[ORM\Column(nullable: true)]
    private ?int $score_sum = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfId(): ?int
    {
        return $this->prof_id;
    }

    public function setProfId(int $prof_id): static
    {
        $this->prof_id = $prof_id;

        return $this;
    }

    public function getMeetingRoom(): ?string
    {
        return $this->meeting_room;
    }

    public function setMeetingRoom(string $meeting_room): static
    {
        $this->meeting_room = $meeting_room;

        return $this;
    }

    public function getMeetingName(): ?string
    {
        return $this->meeting_name;
    }

    public function setMeetingName(string $meeting_name): static
    {
        $this->meeting_name = $meeting_name;

        return $this;
    }

    public function getMeetingStart(): ?\DateTimeInterface
    {
        return $this->meeting_start;
    }

    public function setMeetingStart(\DateTimeInterface $meeting_start): static
    {
        $this->meeting_start = $meeting_start;

        return $this;
    }

    public function getMeetingEnd(): ?\DateTimeInterface
    {
        return $this->meeting_end;
    }

    public function setMeetingEnd(\DateTimeInterface $meeting_end): static
    {
        $this->meeting_end = $meeting_end;

        return $this;
    }

    public function getScoreSum(): ?int
    {
        return $this->score_sum;
    }

    public function setScoreSum(?int $score_sum): static
    {
        $this->score_sum = $score_sum;

        return $this;
    }
}

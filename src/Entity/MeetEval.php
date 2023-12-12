<?php

namespace App\Entity;

use App\Repository\MeetEvalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetEvalRepository::class)]
class MeetEval
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creation_time = null;

    #[ORM\ManyToOne(inversedBy: 'meetEvals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meetings $meeting = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function getCreationTime(): ?\DateTimeInterface
    {
        return $this->creation_time;
    }

    public function setCreationTime(\DateTimeInterface $creation_time): static
    {
        $this->creation_time = $creation_time;

        return $this;
    }

    public function getMeeting(): ?Meetings
    {
        return $this->meeting;
    }

    public function setMeeting(?Meetings $meeting): static
    {
        $this->meeting = $meeting;

        return $this;
    }
}

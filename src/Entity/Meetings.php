<?php

namespace App\Entity;

use App\Repository\MeetingsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingsRepository::class)]
class Meetings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Professor $prof = null;

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

    #[ORM\OneToMany(mappedBy: 'meeting', targetEntity: MeetEval::class)]
    private Collection $meetEvals;

    public function __construct()
    {
        $this->meetEvals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProf(): ?Professor
    {
        return $this->prof;
    }

    public function setProf(?Professor $prof): static
    {
        $this->prof = $prof;

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

    /**
     * @return Collection<int, MeetEval>
     */
    public function getMeetEvals(): Collection
    {
        return $this->meetEvals;
    }

    public function addMeetEval(MeetEval $meetEval): static
    {
        if (!$this->meetEvals->contains($meetEval)) {
            $this->meetEvals->add($meetEval);
            $meetEval->setMeeting($this);
        }

        return $this;
    }

    public function removeMeetEval(MeetEval $meetEval): static
    {
        if ($this->meetEvals->removeElement($meetEval)) {
            // set the owning side to null (unless already changed)
            if ($meetEval->getMeeting() === $this) {
                $meetEval->setMeeting(null);
            }
        }

        return $this;
    }
}

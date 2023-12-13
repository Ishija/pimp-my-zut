<?php

namespace App\Entity;

use App\Repository\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfessorRepository::class)]
class Professor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $teacher = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $total_score = null;

    #[ORM\OneToMany(mappedBy: 'prof', targetEntity: Meetings::class)]
    private Collection $meetings;

    public function __construct()
    {
        $this->meetings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeacher(): ?string
    {
        return $this->teacher;
    }
    public function generateEmail(): void
    {
        $nameParts = explode(' ', $this->teacher);
        $lastName = $nameParts[0] ?? '';
        $firstName = $nameParts[1] ?? '';

        $this->email = strtolower($firstName . '.' . $lastName . '@zut.edu.pl');
    }
    public function setTeacher(string $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTotalScore(): ?int
    {
        return $this->total_score;
    }

    public function setTotalScore(int $total_score): static
    {
        $this->total_score = $total_score;

        return $this;
    }

    /**
     * @return Collection<int, Meetings>
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meetings $meeting): static
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings->add($meeting);
            $meeting->setProf($this);
        }

        return $this;
    }

    public function removeMeeting(Meetings $meeting): static
    {
        if ($this->meetings->removeElement($meeting)) {
            // set the owning side to null (unless already changed)
            if ($meeting->getProf() === $this) {
                $meeting->setProf(null);
            }
        }

        return $this;
    }

}

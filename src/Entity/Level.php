<?php

namespace App\Entity;

use App\Repository\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: LevelRepository::class)]
#[UniqueEntity(
    fields: 'name',
    message: 'le titre du niveau existe déjà.'
)]
class Level
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message: 'Le titre ne peut pas être vide',
    )]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères',
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s']+$/",
        message: 'Le titre du niveau se compose de caractères non autorisés',
    )]
    #[ORM\Column(length: 30, nullable: true)]
    private ?string $name = null;

    #[Assert\NotBlank(
        message: 'Les prérequis ne peuvent pas être vide',
    )]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Les prérequis doivent contenir au moins {{ limit }} caractères',
        maxMessage: 'Les prérequis ne doivent pas dépasser {{ limit }} caractères',
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prerequisite = null;

    /**
     * @var Collection<int, Course>
     */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'level')]
    private Collection $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrerequisite(): ?string
    {
        return $this->prerequisite;
    }

    public function setPrerequisite(?string $prerequisite): static
    {
        $this->prerequisite = $prerequisite;

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setLevel($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getLevel() === $this) {
                $course->setLevel(null);
            }
        }

        return $this;
    }
    //permet de selectionner le nom du niveau par defeaut lorsqu'on l'appel
    public function __toString():string
    {
        return $this->getName();
    }
}

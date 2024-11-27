<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(
    fields: 'name',
    message: 'le titre du cours existe déjà.'
)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message: 'Le titre ne peut pas être vide',
    )]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères',
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s']+$/",
        message: 'Le titre du cours se compose de caractères non autorisés',
    )]
    #[ORM\Column(length: 120, nullable: true)]
    private ?string $name = null;

    #[Assert\NotBlank(
        message: 'La petite description ne peut pas être vide',
    )]
    #[Assert\Length(
        min: 5,
        max: 200,
        minMessage: 'La petite description doit contenir au moins {{ limit }} caractères',
        maxMessage: 'La petite description ne doit pas dépasser {{ limit }} caractères',
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $small_description = null;

    #[Assert\NotBlank(
        message: 'La longue description ne peut pas être vide',
    )]
    #[Assert\Length(
        min: 5,
        minMessage: 'La longue description doit contenir au moins {{ limit }} caractères',
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $full_description = null;

    #[Assert\NotBlank(
        message: 'La durée ne peut pas être vide.',
    )]
    #[Assert\Length(
        min: 5,
        max: 60,
        minMessage: 'La durée doit contenir au moins {{ limit }} caractères',
        maxMessage: 'La durée ne doit pas dépasser {{ limit }} caractères',
    )]
    #[ORM\Column(length: 60, nullable: true)]
    private ?string $duration = null;


    #[Assert\PositiveOrZero(
        message: "Le prix doit être un nombre strictement positif."
    )]
    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?bool $is_published = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[Assert\File(
        maxSize : "2M",
        mimeTypes : ["image/jpeg", "image/png", "image/gif"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, GIF)."
    )]
    #[Vich\UploadableField(mapping: 'course_image', fileNameProperty: 'image')]
    private ?File $imageFile = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $program = null;

    #[Assert\File(
        maxSize : "10M",
        mimeTypes : ["application/pdf"],
        mimeTypesMessage: "Veuillez télécharger un fichier PDF valide."
    )]
    #[Vich\UploadableField(mapping: 'course_program', fileNameProperty: 'program')]
    private ?File $programFile = null;

    #[Assert\NotBlank(
        message: 'La categorie ne peut pas être vide',
    )]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    private ?category $category = null;

    #[Assert\NotBlank(
        message: 'Le niveau ne peut pas être vide',
    )]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    private ?level $level = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'course')]
    private Collection $comments;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Basket>
     */
    #[ORM\OneToMany(targetEntity: Basket::class, mappedBy: 'course')]
    private Collection $baskets;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'course')]
    private Collection $users;

    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s',\-0-9]+$/",
        message: 'L\'horaire du cours se compose de caractères non autorisés',
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $schedule = null;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->baskets = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getSmallDescription(): ?string
    {
        return $this->small_description;
    }

    public function setSmallDescription(?string $small_description): static
    {
        $this->small_description = $small_description;

        return $this;
    }

    public function getFullDescription(): ?string
    {
        return $this->full_description;
    }

    public function setFullDescription(?string $full_description): static
    {
        $this->full_description = $full_description;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setPublished(?bool $is_published): static
    {
        $this->is_published = $is_published;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
    }
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getProgram(): ?string
    {
        return $this->program;
    }

    public function setProgram(?string $program): static
    {
        $this->program = $program;

        return $this;
    }

    public function setProgramFile(?File $programFile = null): void
    {
        $this->programFile = $programFile;

        if (null !== $programFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
    }
    public function getProgramFile(): ?File
    {
        return $this->programFile;
    }

    public function getCategory(): ?category
    {
        return $this->category;
    }

    public function setCategory(?category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLevel(): ?level
    {
        return $this->level;
    }

    public function setLevel(?level $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCourse($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCourse() === $this) {
                $comment->setCourse(null);
            }
        }

        return $this;
    }
    //permet de selectionner le nom du cours par defeaut lorsqu'on l'appel
    public function __toString():string
    {
        return $this->getName();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Basket>
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Basket $basket): static
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets->add($basket);
            $basket->setCourse($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): static
    {
        if ($this->baskets->removeElement($basket)) {
            // set the owning side to null (unless already changed)
            if ($basket->getCourse() === $this) {
                $basket->setCourse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addCourse($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeCourse($this);
        }

        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): static
    {
        $this->schedule = $schedule;

        return $this;
    }


}

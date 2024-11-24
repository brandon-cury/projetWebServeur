<?php
namespace App\Class;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    #[Assert\NotBlank]
    private string $firstName;
    #[Assert\NotBlank]
    private string $lastName;
    #[Assert\NotBlank]
    #[Assert\Email(
        message: 'Votre Email {{ value }} n\'est pas valide.',
    )]
    private string $email;
    #[Assert\NotBlank]
    private string $subject;
    #[Assert\NotBlank]
    private string $message;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $administrator = null;

    public function getAdministrator(): ?User
    {
        return $this->administrator;
    }

    public function setAdministrator(?User $administrator): static
    {
        $this->administrator = $administrator;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }


}
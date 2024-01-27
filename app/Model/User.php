<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private null|int $id = null;

    #[ORM\Column(type: 'string')]
    private string $name = '';

    #[ORM\Column(type: 'string', unique: true)]
    private string $email = '';

    #[ORM\Column(type: 'string')]
    private string $password = '';

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    /** @var Collection<string, Contact> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Contact::class)]
    private Collection $contacts;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->contacts = new ArrayCollection();
    }

    public function getId(): null|int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getHashPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_ARGON2ID);
    }

    public function checkPasswordIsCorrect(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function addContact(Contact $contact): void
    {
        $this->contacts[] = $contact;
    }

    /** @return Collection<string, Contact> */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }
}

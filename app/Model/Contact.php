<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'contacts')]
class Contact
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
    private string $address = '';

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<string, Phone> */
    #[ORM\OneToMany(mappedBy: 'contact', targetEntity: Phone::class)]
    private Collection $phones;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->phones = new ArrayCollection();
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function addPhone(Phone $phone): void
    {
        $this->phones[] = $phone;
    }

    /** @return Collection<string, Phone> */
    public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}

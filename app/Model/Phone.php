<?php

namespace App\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'phones')]
class Phone implements \JsonSerializable
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private null|int $id = null;

    #[Column(type: 'string', length: 11)]
    private string $number = '';

    #[ManyToOne(targetEntity: Contact::class, inversedBy: 'phones')]
    #[JoinColumn(name: 'contact_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Contact $contact;

    public function getId(): null|int
    {
        return $this->id;
    }

    public function getPhone(): string
    {
        return $this->number;
    }

    public function setPhone(string $number): void
    {
        $this->number = $number;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): void
    {
        $this->contact = $contact;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
        ];
    }
}

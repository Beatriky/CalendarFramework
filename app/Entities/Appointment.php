<?php

namespace App\app\Entities;

use App\Entities\BaseEntity;
use App\Entities\Location;
use App\Entities\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping AS ORM;

#[ORM\Entity]
#[ORM\Table('appointments')]

class Appointment extends \App\Entities\BaseEntity
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(name: 'date', type: Types::STRING, length: 255, nullable: false)]
    protected string $date;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 255, nullable: false)]
    protected string $city;

    #[ORM\ManyToOne(targetEntity: Location::class, fetch: 'eager', inversedBy: 'appointments')]
    #[ORM\JoinColumn(name: 'idLocation', referencedColumnName: 'id', nullable: false)]
    protected Location $location;

    #[ORM\ManyToOne(targetEntity: User::class,fetch: 'eager', inversedBy: 'appointments')]
    #[ORM\JoinColumn(name: 'idUser', referencedColumnName: 'id', nullable: false)]
    protected User $user;
}



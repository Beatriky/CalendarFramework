<?php

namespace App\Entities;

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

    #[ORM\Column(name: 'date', type: Types::DATE_MUTABLE, length: 255, nullable: false)]
    protected \DateTime $date;

    #[ORM\ManyToOne(targetEntity: Location::class, inversedBy: 'appointments')]
    #[ORM\JoinColumn(name: 'idLocation', referencedColumnName: 'id', nullable: false)]
    protected Location $location;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'appointments')]
    #[ORM\JoinColumn(name: 'idUser', referencedColumnName: 'id', nullable: false)]
    protected User $user;
}



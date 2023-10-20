<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Task
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $marking = null;

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        public readonly string $title = 'Title',
    ) {}
}

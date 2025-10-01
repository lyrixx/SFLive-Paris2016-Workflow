<?php

namespace App\Entity;

use App\Entity\Model\TaskStep;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Task
{
    #[ORM\Column()]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    public int $id;

    #[ORM\Column(nullable: true)]
    public ?TaskStep $marking = null;

    public function __construct(
        #[ORM\Column()]
        public readonly string $title = 'Title',
    ) {
    }
}

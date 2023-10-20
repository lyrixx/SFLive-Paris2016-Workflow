<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Article
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\Column(type: 'json', nullable: true)]
    public ?array $marking = null;

    #[ORM\Column(type: 'json', nullable: true)]
    public array $transitionContexts = [];

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        public readonly string $title = 'Title',
    ) {}

    public function setMarking(?array $marking, array $context = []): void
    {
        $this->marking = $marking;
        $this->transitionContexts[] = [
            'new_marking' => $marking,
            'context' => $context,
            'time' => (new \DateTime())->format('c'),
        ];
    }
}

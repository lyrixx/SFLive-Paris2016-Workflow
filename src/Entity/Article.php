<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Article
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", length=255) */
    private $title;

    /** @ORM\Column(type="json", nullable=true) */
    private $marking;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $transitionContexts;

    public function __construct($title = 'Title')
    {
        $this->title = $title;
        $this->transitionContexts = [];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getMarking()
    {
        return $this->marking;
    }

    public function setMarking($marking, $context = [])
    {
        $this->marking = $marking;
        $this->transitionContexts[] = [
            'new_marking' => $marking,
            'context' => $context,
            'time' => (new \DateTime())->format('c'),
        ];
    }

    public function getTransitionContexts()
    {
        return $this->transitionContexts;
    }

    public function setTransitionContexts($transitionContexts)
    {
        $this->transitionContexts = $transitionContexts;
    }
}

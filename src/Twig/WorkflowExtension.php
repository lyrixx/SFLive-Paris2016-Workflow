<?php

namespace App\Twig;

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WorkflowExtension extends AbstractExtension
{
    public function __construct(
        private readonly ContainerInterface $workflows,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('workflow_all_transitions', $this->getTransitions(...)),
        ];
    }

    // This method is a hack to get all transitions, enabled or not.
    // This should be done only for a demo purpose
    public function getTransitions(string $name): array
    {
        return $this->workflows->get($name)->getDefinition()->getTransitions();
    }
}

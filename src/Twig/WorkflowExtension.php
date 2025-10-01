<?php

namespace App\Twig;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\Workflow\Dumper\MermaidDumper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WorkflowExtension extends AbstractExtension
{
    public function __construct(
        #[TaggedLocator('workflow', indexAttribute: 'name')]
        private readonly ContainerInterface $workflows,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('workflow_all_transitions', $this->getTransitions(...)),
            new TwigFunction('workflow_dump', $this->dump(...)),
        ];
    }

    // This method is a hack to get all transitions, enabled or not.
    // This should be done only for a demo purpose
    public function getTransitions(string $name): array
    {
        return $this->workflows->get($name)->getDefinition()->getTransitions();
    }

    public function dump(string $name, string $transitionType, ?object $subject = null): string
    {
        $dumper = new MermaidDumper($transitionType);
        $workflow = $this->workflows->get($name);

        return $dumper->dump(
            $workflow->getDefinition(),
            $subject ? $workflow->getMarking($subject) : null,
        );
    }
}

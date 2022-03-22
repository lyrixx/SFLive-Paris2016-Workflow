<?php

namespace App\Twig;

use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\TransitionBlockerList;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WorkflowExtension extends AbstractExtension
{
    public function __construct(
        private Registry $workflowRegistry
    ) {
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('workflow_all_transitions', [$this, 'getTransitions']),
            new TwigFunction('workflow_build_transition_blocker_list', [$this, 'buildTransitionBlockerList']),
        );
    }

    // This method is a hack to get all transitions, enabled or not.
    // This should be done only for a demo purpose
    public function getTransitions(object $subject, string $name = null): array
    {
        $workflow = $this->workflowRegistry->get($subject, $name);

        return $workflow->getDefinition()->getTransitions();
    }

    public function buildTransitionBlockerList(object $subject, string $transitionName, string $name = null): TransitionBlockerList
    {
        $workflow = $this->workflowRegistry->get($subject, $name);

        return $workflow->buildTransitionBlockerList($subject, $transitionName);
    }
}

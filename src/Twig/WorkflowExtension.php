<?php

namespace App\Twig;

use Symfony\Component\Workflow\Registry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WorkflowExtension extends AbstractExtension
{
    private $workflowRegistry;

    public function __construct(Registry $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('workflow_all_transitions', [$this, 'getTransitions']),
            new TwigFunction('workflow_build_transition_blocker_list', [$this, 'buildTransitionBlockerList']),
        );
    }

    // This method is a hack to get all transitions, enabled or not.
    // This should be done only for a demo purpose
    public function getTransitions($subject, string $name = null)
    {
        $workflow = $this->workflowRegistry->get($subject, $name);

        return $workflow->getDefinition()->getTransitions();
    }

    public function buildTransitionBlockerList($subject, string $transitionName, string $name = null)
    {
        $workflow = $this->workflowRegistry->get($subject, $name);

        return $workflow->buildTransitionBlockerList($subject, $transitionName);
    }
}

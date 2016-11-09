<?php

namespace AppBundle\Twig;

use Symfony\Component\Workflow\Registry;

class WorkflowExtension extends \Twig_Extension
{
    private $workflowRegistry;

    public function __construct(Registry $workflowRegistry)
    {
        $this->workflowRegistry = $workflowRegistry;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('workflow_all_transitions', [$this, 'getTransitions']),
        );
    }

    // This method is a hack to get all transitions, enable-able or not.
    // This should be done only for a demo purpose
    public function getTransitions($object, $name = null)
    {
        $workflow = $this->workflowRegistry->get($object, $name);

        return $workflow->getDefinition()->getTransitions();
    }

    public function getName()
    {
        return 'workflow_developer';
    }
}

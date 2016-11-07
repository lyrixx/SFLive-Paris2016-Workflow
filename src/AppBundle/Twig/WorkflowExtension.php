<?php

namespace AppBundle\Twig;

use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

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
            new \Twig_SimpleFunction('workflow_transitions', [$this, 'getTransitions']),
        );
    }

    // This method is a hack to get all transitions, enable-able or not.
    // This should be done only for a demo purpose
    public function getTransitions($object, $name = null)
    {
        $workflow = $this->workflowRegistry->get($object, $name);

        $definition = $this->getProperty($workflow, 'definition');

        return $definition->getTransitions();
    }

    public function getName()
    {
        return 'workflow_developer';
    }


    private function getProperty($object, $property)
    {
        $reflectionProperty = new \ReflectionProperty(Workflow::class, $property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}

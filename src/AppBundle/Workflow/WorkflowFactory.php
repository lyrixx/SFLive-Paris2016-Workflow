<?php

namespace AppBundle\Workflow;

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\PropertyAccessorMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

class WorkflowFactory
{
    public static function createWorkflow()
    {
        $definition = new Definition();

        $definition->addPlaces(range('a', 'g'));

        $definition->addTransition(new Transition('t1', 'a',        ['b', 'c']));
        $definition->addTransition(new Transition('t2', ['b', 'c'],  'd'));
        $definition->addTransition(new Transition('t3', 'd',         'e'));
        $definition->addTransition(new Transition('t4', 'd',         'f'));
        $definition->addTransition(new Transition('t5', 'e',         'g'));
        $definition->addTransition(new Transition('t6', 'f',         'g'));

        $workflow = new Workflow($definition, new PropertyAccessorMarkingStore());

        return $workflow;
    }
}

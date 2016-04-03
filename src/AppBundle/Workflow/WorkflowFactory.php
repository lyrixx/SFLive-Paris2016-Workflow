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

        $definition->addPlaces([
            'draft',
            'wait_for_journalist',
            'approved_by_journalist',
            'wait_for_spellchecker',
            'approved_by_spellchecker',
            'published',
        ]);

        $definition->addTransition(new Transition('request_review', 'draft', ['wait_for_journalist', 'wait_for_spellchecker']));
        $definition->addTransition(new Transition('journalist_approval', 'wait_for_journalist', 'approved_by_journalist'));
        $definition->addTransition(new Transition('spellchecker_approval', 'wait_for_spellchecker', 'approved_by_spellchecker'));
        $definition->addTransition(new Transition('publish', ['approved_by_journalist', 'approved_by_spellchecker'], 'published'));

        $workflow = new Workflow($definition, new PropertyAccessorMarkingStore());

        return $workflow;
    }
}

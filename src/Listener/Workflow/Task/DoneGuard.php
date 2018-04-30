<?php

namespace App\Listener\Workflow\Task;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

class DoneGuard implements EventSubscriberInterface
{
    public function guardPublish(GuardEvent $event)
    {
        $timeLimit = $event->getMetadata('time_limit', $event->getTransition());

        if (date('Hi') <= $timeLimit) {
            return;
        }

        $explanation = $event->getMetadata('explanation', $event->getTransition());
        $event->addTransitionBlocker(new TransitionBlocker($explanation , 0));
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.task.guard.done' => 'guardPublish',
        ];
    }
}

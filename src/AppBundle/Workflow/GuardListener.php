<?php

namespace AppBundle\Workflow;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class GuardListener implements EventSubscriberInterface
{
    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function onTransition(GuardEvent $event)
    {
        // For all action, user should be logger
        if (!$this->checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $event->setBlocked(true);
        }
    }

    public function onTransitionJournalist(GuardEvent $event)
    {
        if (!$this->checker->isGranted('ROLE_JOURNALIST')) {
            $event->setBlocked(true);
        }
    }

    public function onTransitionSpellChecker(GuardEvent $event)
    {
        if (!$this->checker->isGranted('ROLE_SPELLCHECKER')) {
            $event->setBlocked(true);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'workflow.article.guard' => 'onTransition',
            'workflow.article.guard.journalist_approval' => 'onTransitionJournalist',
            'workflow.article.guard.spellchecker_approval' => 'onTransitionSpellChecker',
        );
    }
}

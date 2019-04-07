<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class TransitionEventSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    public function onWorkflowArticleTransition(TransitionEvent $event)
    {
        $context = $event->getContext();

        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof UserInterface) {
            $context['user'] = $user->getUsername();
        }

        $event->setContext($context);
    }

    public static function getSubscribedEvents()
    {
        return [
           TransitionEvent::class => 'onWorkflowArticleTransition',
        ];
    }
}

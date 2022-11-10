<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class TransitionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function onWorkflowArticleTransition(TransitionEvent $event): void
    {
        $context = $event->getContext();

        $user = $this->tokenStorage->getToken()?->getUser();
        if ($user instanceof UserInterface) {
            $context['user'] = $user->getUserIdentifier();
        }

        $event->setContext($context);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::class => 'onWorkflowArticleTransition',
        ];
    }
}

<?php

namespace App\EventSubscriber;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Event\TransitionEvent;

class TransitionEventSubscriber
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    #[AsTransitionListener()]
    #[AsTransitionListener(workflow: 'my-workflow')]
    #[AsTransitionListener(workflow: 'my-workflow', transition: 'some-transition')]
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

<?php

namespace App\Workflow;

use App\Entity\Article;
use App\Workflow\Validator\ArticleValidator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\Attribute\AsTransitionListener;
use Symfony\Component\Workflow\Attribute\AsWorkflow;
use Symfony\Component\Workflow\Attribute\Place;
use Symfony\Component\Workflow\Attribute\Transition;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\WorkflowTrait;
use Symfony\Component\Workflow\WorkflowType;

// The name of the workflow ("article") is derived from the class name.
// The places are inferred from the transitions, the #[Place] attributes only
// add their metadata.
#[AsWorkflow(
    type: WorkflowType::Workflow,
    supports: Article::class,
    metadata: ['title' => 'Manage article'],
    auditTrail: true,
    definitionValidators: [ArticleValidator::class],
)]
class ArticleWorkflow
{
    use WorkflowTrait;

    #[Place(metadata: ['title' => 'Draft'])]
    public const string DRAFT = 'draft';

    #[Place(metadata: ['title' => 'Waiting for Journalist review'])]
    public const string WAITING_FOR_JOURNALIST = 'waiting for journalist';

    #[Place(metadata: ['title' => 'Approved By Journalist'])]
    public const string APPROVED_BY_JOURNALIST = 'approved by journalist';

    #[Place(metadata: ['title' => 'Waiting for Spellchecker review'])]
    public const string WAIT_FOR_SPELLCHECKER = 'wait for spellchecker';

    #[Place(metadata: ['title' => 'Approved By Spellchecker'])]
    public const string APPROVED_BY_SPELLCHECKER = 'approved by spellchecker';

    public const string PUBLISHED = 'published';

    #[Transition(
        from: self::DRAFT,
        to: [self::WAITING_FOR_JOURNALIST, self::WAIT_FOR_SPELLCHECKER],
        guard: 'is_fully_authenticated()',
        metadata: ['title' => 'Do you want a Review?'],
    )]
    public const string REQUEST_REVIEW = 'request review';

    #[Transition(
        from: self::WAITING_FOR_JOURNALIST,
        to: self::APPROVED_BY_JOURNALIST,
        guard: "is_granted('ROLE_JOURNALIST')",
        metadata: ['title' => 'Do you valid the article?'],
    )]
    public const string JOURNALIST_APPROVAL = 'journalist approval';

    #[Transition(
        from: self::WAIT_FOR_SPELLCHECKER,
        to: self::APPROVED_BY_SPELLCHECKER,
        guard: "is_fully_authenticated() and is_granted('ROLE_SPELLCHECKER')",
        metadata: ['title' => 'Do you valid the spell check?'],
    )]
    public const string SPELLCHECKER_APPROVAL = 'spellchecker approval';

    #[Transition(
        from: [self::APPROVED_BY_JOURNALIST, self::APPROVED_BY_SPELLCHECKER],
        to: self::PUBLISHED,
        guard: 'is_fully_authenticated()',
        metadata: ['title' => 'Do you want to publish?'],
    )]
    public const string PUBLISH = 'publish';

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * Without "workflow", the listener listens to the transitions of this workflow only.
     *
     * @param TransitionEvent<Article> $event
     */
    #[AsTransitionListener]
    public function addUserToContext(TransitionEvent $event): void
    {
        $context = $event->getContext();

        $user = $this->tokenStorage->getToken()?->getUser();
        if ($user instanceof UserInterface) {
            $context['user'] = $user->getUserIdentifier();
        }

        $event->setContext($context);
    }
}

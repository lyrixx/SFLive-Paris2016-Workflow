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
// add their description and metadata, and mark the initial place.
#[AsWorkflow(
    type: WorkflowType::Workflow,
    supports: Article::class,
    description: 'Publishes an article once a journalist and a spellchecker have both reviewed it, in parallel.',
    metadata: ['title' => 'Manage article'],
    auditTrail: true,
    definitionValidators: [ArticleValidator::class],
)]
class ArticleWorkflow
{
    use WorkflowTrait;

    #[Place(
        description: 'The article is being written and has not been submitted for review yet.',
        metadata: ['title' => 'Draft'],
        initial: true,
    )]
    public const string DRAFT = 'draft';

    #[Place(
        description: 'The article waits for a journalist to review its content.',
        metadata: ['title' => 'Waiting for Journalist review'],
    )]
    public const string WAITING_FOR_JOURNALIST = 'waiting for journalist';

    #[Place(
        description: 'A journalist has approved the content of the article.',
        metadata: ['title' => 'Approved By Journalist'],
    )]
    public const string APPROVED_BY_JOURNALIST = 'approved by journalist';

    #[Place(
        description: 'The article waits for a spellchecker to review its spelling.',
        metadata: ['title' => 'Waiting for Spellchecker review'],
    )]
    public const string WAIT_FOR_SPELLCHECKER = 'wait for spellchecker';

    #[Place(
        description: 'A spellchecker has approved the spelling of the article.',
        metadata: ['title' => 'Approved By Spellchecker'],
    )]
    public const string APPROVED_BY_SPELLCHECKER = 'approved by spellchecker';

    #[Place(description: 'The article is published and visible to the readers.')]
    public const string PUBLISHED = 'published';

    #[Transition(
        from: self::DRAFT,
        to: [self::WAITING_FOR_JOURNALIST, self::WAIT_FOR_SPELLCHECKER],
        description: 'Submits the draft to a journalist and a spellchecker, who review it in parallel.',
        guard: 'is_fully_authenticated()',
        metadata: ['title' => 'Do you want a Review?'],
    )]
    public const string REQUEST_REVIEW = 'request review';

    #[Transition(
        from: self::WAITING_FOR_JOURNALIST,
        to: self::APPROVED_BY_JOURNALIST,
        description: 'A journalist approves the content of the article.',
        guard: "is_granted('ROLE_JOURNALIST')",
        metadata: ['title' => 'Do you valid the article?'],
    )]
    public const string JOURNALIST_APPROVAL = 'journalist approval';

    #[Transition(
        from: self::WAIT_FOR_SPELLCHECKER,
        to: self::APPROVED_BY_SPELLCHECKER,
        description: 'A spellchecker approves the spelling of the article.',
        guard: "is_fully_authenticated() and is_granted('ROLE_SPELLCHECKER')",
        metadata: ['title' => 'Do you valid the spell check?'],
    )]
    public const string SPELLCHECKER_APPROVAL = 'spellchecker approval';

    #[Transition(
        from: [self::APPROVED_BY_JOURNALIST, self::APPROVED_BY_SPELLCHECKER],
        to: self::PUBLISHED,
        description: 'Publishes the article once both the journalist and the spellchecker have approved it.',
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

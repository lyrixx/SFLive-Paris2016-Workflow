<?php

namespace App\Entity\Model;

use Symfony\Component\Workflow\Attribute\Place;

enum TaskStep: string
{
    #[Place(
        description: 'The task is newly created and not yet processed.',
        metadata: ['label' => 'New'],
        initial: true,
    )]
    case New = 'new';

    #[Place(
        description: 'The task is backlogged due to a temporary error, waiting to be retried.',
        metadata: ['label' => 'Backlogged'],
    )]
    case Backlogged = 'backlogged';

    #[Place(
        description: 'The task is currently being processed.',
        metadata: ['label' => 'Processing'],
    )]
    case Processing = 'processing';

    #[Place(
        description: 'The task has failed permanently and will not be retried.',
        metadata: ['label' => 'Failed'],
    )]
    case Failed = 'failed';

    #[Place(
        description: 'The task has been completed successfully.',
        metadata: ['label' => 'Completed'],
    )]
    case Completed = 'completed';
}

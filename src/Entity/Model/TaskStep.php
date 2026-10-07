<?php

namespace App\Entity\Model;

use Symfony\Component\Workflow\Attribute\Place;

enum TaskStep: string
{
    #[Place(metadata: [
        'label' => 'New',
        'description' => 'The task is newly created and not yet processed',
    ])]
    case New = 'new';

    #[Place(metadata: [
        'label' => 'Backlogged',
        'description' => 'The task is backlogged due to temporary issues',
    ])]
    case Backlogged = 'backlogged';

    #[Place(metadata: [
        'label' => 'Processing',
        'description' => 'The task is currently being processed',
    ])]
    case Processing = 'processing';

    #[Place(metadata: [
        'label' => 'Failed',
        'description' => 'The task has failed permanently',
    ])]
    case Failed = 'failed';

    #[Place(metadata: [
        'label' => 'Completed',
        'description' => 'The task has been completed successfully',
    ])]
    case Completed = 'completed';
}

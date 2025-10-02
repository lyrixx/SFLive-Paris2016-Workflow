<?php

namespace App\Workflow;

use App\Entity\Model\TaskStep;
use Symfony\Component\Workflow\Attribute\AsWorkflow;
use Symfony\Component\Workflow\Attribute\Transition;

#[AsWorkflow(
    name: 'task',
    supports: [\App\Entity\Task::class],
    // places: TaskStep::class, // Not needed, but works!
)]
class TaskWorkflow extends \Symfony\Component\Workflow\Workflow
{
    #[Transition(
        from: TaskStep::New,
        to: TaskStep::Processing,
    )]
    public const string START_PROCESS = 'start_process';

    #[Transition(
        from: TaskStep::Backlogged,
        to: TaskStep::Processing,
    )]
    public const string RETRY = 'retry';

    #[Transition(
        from: TaskStep::Processing,
        to: TaskStep::Backlogged,
    )]
    public const string TEMP_ERROR = 'temp_error';

    #[Transition(
        from: TaskStep::Processing,
        to: TaskStep::Failed,
    )]
    public const string PERMANENT_ERROR = 'permanent_error';

    #[Transition(
        from: TaskStep::Processing,
        to: TaskStep::Completed,
    )]
    public const string COMPLETE_WITHOUT_ERROR = 'complete_without_error';
}

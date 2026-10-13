<?php

namespace App\Workflow;

use App\Entity\Model\TaskStep;
use App\Entity\Task;
use Symfony\Component\Workflow\Attribute\AsWorkflow;
use Symfony\Component\Workflow\Attribute\Transition;
use Symfony\Component\Workflow\WorkflowTrait;

// The name of the state machine ("task") is derived from the class name.
// The places are inferred from the transitions, the "places" argument is not
// needed, but it enforces every place to be a case of the enum.
#[AsWorkflow(
    supports: Task::class,
    description: 'Processes a task, retrying it after temporary errors until it completes or fails permanently.',
    places: TaskStep::class,
    auditTrail: true,
)]
class TaskWorkflow
{
    use WorkflowTrait;

    #[Transition(
        from: TaskStep::New,
        to: TaskStep::Processing,
        description: 'Starts processing a new task.',
    )]
    public const string START_PROCESS = 'start_process';

    #[Transition(
        from: TaskStep::Backlogged,
        to: TaskStep::Processing,
        description: 'Retries a backlogged task.',
    )]
    public const string RETRY = 'retry';

    #[Transition(
        from: TaskStep::Processing,
        to: TaskStep::Backlogged,
        description: 'Puts the task in the backlog after a temporary error, to retry it later.',
    )]
    public const string TEMP_ERROR = 'temp_error';

    #[Transition(
        from: TaskStep::Processing,
        to: TaskStep::Failed,
        description: 'Marks the task as failed after an error that a retry cannot fix.',
    )]
    public const string PERMANENT_ERROR = 'permanent_error';

    #[Transition(
        from: TaskStep::Processing,
        to: TaskStep::Completed,
        description: 'Marks the task as completed once it has been processed without error.',
    )]
    public const string COMPLETE_WITHOUT_ERROR = 'complete_without_error';
}

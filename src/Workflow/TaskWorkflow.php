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
    places: TaskStep::class,
    auditTrail: true,
)]
class TaskWorkflow
{
    use WorkflowTrait;

    #[Transition(from: TaskStep::New, to: TaskStep::Processing)]
    public const string START_PROCESS = 'start_process';

    #[Transition(from: TaskStep::Backlogged, to: TaskStep::Processing)]
    public const string RETRY = 'retry';

    #[Transition(from: TaskStep::Processing, to: TaskStep::Backlogged)]
    public const string TEMP_ERROR = 'temp_error';

    #[Transition(from: TaskStep::Processing, to: TaskStep::Failed)]
    public const string PERMANENT_ERROR = 'permanent_error';

    #[Transition(from: TaskStep::Processing, to: TaskStep::Completed)]
    public const string COMPLETE_WITHOUT_ERROR = 'complete_without_error';
}

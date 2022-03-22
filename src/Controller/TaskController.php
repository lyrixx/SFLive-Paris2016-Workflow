<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Exception\ExceptionInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @Route("/", name="task_index")
     */
    public function index()
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $this->em->getRepository(Task::class)->findAll(),
        ]);
    }

    /**
     * @Route("/create", methods={"POST"}, name="task_create")
     */
    public function create(Request $request)
    {
        $task = new Task($request->request->get('title', 'title'));

        $this->em->persist($task);
        $this->em->flush();

        return $this->redirect($this->generateUrl('task_show', ['id' => $task->getId()]));
    }

    /**
     * @Route("/show/{id}", name="task_show")
     */
    public function show(Task $task)
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/apply-transition/{id}", methods={"POST"}, name="task_apply_transition")
     */
    public function applyTransition(WorkflowInterface $taskStateMachine, Request $request, Task $task)
    {
        try {
            $taskStateMachine->apply($task, $request->request->get('transition'));

            $this->em->flush();
        } catch (ExceptionInterface $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('task_show', ['id' => $task->getId()])
        );
    }

    /**
     * @Route("/reset-marking/{id}", methods={"POST"}, name="task_reset_marking")
     */
    public function resetMarking(Task $task)
    {
        $task->setMarking(null);
        $this->em->flush();

        return $this->redirect($this->generateUrl('task_show', ['id' => $task->getId()]));
    }
}

<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Exception\ExceptionInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task_index")
     */
    public function index()
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $this->get('doctrine')->getRepository(Task::class)->findAll(),
        ]);
    }

    /**
     * @Route("/create", methods={"POST"}, name="task_create")
     */
    public function create(Request $request)
    {
        $task = new Task($request->request->get('title', 'title'));

        $em = $this->get('doctrine')->getManager();
        $em->persist($task);
        $em->flush();

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

            $this->get('doctrine')->getManager()->flush();
        } catch (ExceptionInterface $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
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
        $this->get('doctrine')->getManager()->flush();

        return $this->redirect($this->generateUrl('task_show', ['id' => $task->getId()]));
    }
}

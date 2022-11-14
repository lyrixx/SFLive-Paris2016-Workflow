<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Exception\ExceptionInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route(path: '/task')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route(path: '/', name: 'task_index')]
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $this->em->getRepository(Task::class)->findAll(),
        ]);
    }

    #[Route(path: '/create', methods: ['POST'], name: 'task_create')]
    public function create(Request $request): Response
    {
        $task = new Task($request->request->getAlnum('title', 'title'));

        $this->em->persist($task);
        $this->em->flush();

        return $this->redirectToRoute('task_show', ['id' => $task->id]);
    }

    #[Route(path: '/show/{id}', name: 'task_show')]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route(path: '/apply-transition/{id}', methods: ['POST'], name: 'task_apply_transition')]
    public function applyTransition(WorkflowInterface $taskStateMachine, Request $request, Task $task): Response
    {
        try {
            $taskStateMachine->apply($task, (string) $request->request->get('transition'));

            $this->em->flush();
        } catch (ExceptionInterface $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('task_show', ['id' => $task->id]);
    }

    #[Route(path: '/reset-marking/{id}', methods: ['POST'], name: 'task_reset_marking')]
    public function resetMarking(Task $task): Response
    {
        $task->setMarking(null);
        $this->em->flush();

        return $this->redirectToRoute('task_show', ['id' => $task->id]);
    }
}

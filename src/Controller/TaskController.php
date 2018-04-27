<?php

namespace App\Controller;

use App\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Exception\ExceptionInterface;

/**
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/", name="task_index")
     */
    public function indexAction()
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $this->get('doctrine')->getRepository('App:Task')->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="task_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
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
    public function showAction(Task $task)
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/apply-transition/{id}", name="task_apply_transition")
     * @Method("POST")
     */
    public function applyTransitionAction(Request $request, Task $task)
    {
        try {
            $this->get('state_machine.task')
                ->apply($task, $request->request->get('transition'));

            $this->get('doctrine')->getManager()->flush();
        } catch (ExceptionInterface $e) {
            $request->getSession()->getFlashBag()->add('danger', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('task_show', ['id' => $task->getId()])
        );
    }

    /**
     * @Route("/reset-marking/{id}", name="task_reset_marking")
     * @Method("POST")
     */
    public function resetMarkingAction(Task $task)
    {
        $task->setMarking(null);
        $this->get('doctrine')->getManager()->flush();

        return $this->redirect($this->generateUrl('task_show', ['id' => $task->getId()]));
    }
}

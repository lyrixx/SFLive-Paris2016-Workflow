<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Exception\ExceptionInterface;

/**
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * @Route("", name="article_index")
     */
    public function indexAction()
    {
        return $this->render('article/index.html.twig', [
            'articles' => $this->get('doctrine')->getRepository('App:Article')->findAll(),
        ]);
    }

    /**
     * @Route("/create", methods={"POST"}, name="article_create")
     */
    public function createAction(Request $request)
    {
        $article = new Article($request->request->get('title', 'title'));

        $em = $this->get('doctrine')->getManager();
        $em->persist($article);
        $em->flush();

        return $this->redirect($this->generateUrl('article_show', ['id' => $article->getId()]));
    }

    /**
     * @Route("/show/{id}", name="article_show")
     */
    public function showAction(Article $article)
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/apply-transition/{id}", methods={"POST"}, name="article_apply_transition")
     */
    public function applyTransitionAction(Request $request, Article $article)
    {
        try {
            $this->get('workflow.article')
                ->apply($article, $request->request->get('transition'), [
                    'user' => $this->getUser()->getUsername(),
                ]);

            $this->get('doctrine')->getManager()->flush();
        } catch (ExceptionInterface $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('article_show', ['id' => $article->getId()])
        );
    }

    /**
     * @Route("/reset-marking/{id}", methods={"POST"}, name="article_reset_marking")
     */
    public function resetMarkingAction(Article $article)
    {
        $article->setMarking([]);
        $this->get('doctrine')->getManager()->flush();

        return $this->redirect($this->generateUrl('article_show', ['id' => $article->getId()]));
    }
}

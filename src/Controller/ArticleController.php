<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Exception\ExceptionInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route(path: '/articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        #[Target('article')]
        private readonly WorkflowInterface $workflow,
    ) {}

    #[Route(path: '', name: 'article_index')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $this->em->getRepository(Article::class)->findAll(),
        ]);
    }

    #[Route(path: '/create', methods: ['POST'], name: 'article_create')]
    public function create(Request $request): Response
    {
        $article = new Article($request->request->getAlnum('title', 'title'));

        $this->em->persist($article);
        $this->em->flush();

        return $this->redirectToRoute('article_show', ['id' => $article->id]);
    }

    #[Route(path: '/show/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route(path: '/apply-transition/{id}', methods: ['POST'], name: 'article_apply_transition')]
    public function applyTransition(Request $request, Article $article): Response
    {
        try {
            $this->workflow
                ->apply($article, (string) $request->request->get('transition'), [
                    'time' => date('y-m-d H:i:s'),
                ])
            ;
            $this->em->flush();
        } catch (ExceptionInterface $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('article_show', ['id' => $article->id]);
    }

    #[Route(path: '/reset-marking/{id}', methods: ['POST'], name: 'article_reset_marking')]
    public function resetMarking(Article $article): Response
    {
        $article->setMarking([]);
        $article->transitionContexts = [];

        $this->em->flush();

        return $this->redirectToRoute('article_show', ['id' => $article->id]);
    }
}

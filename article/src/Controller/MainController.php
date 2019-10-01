<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(ArticleRepository $arcRep)
    {
        $article= $arcRep->findBy([],['id'=>'DESC']);
        return $this->render('main/index.html.twig', [
            'articles'=> $article,
        ]);
    }

    /**
     * @Route("/new-comment/{id}", name="comment")
     */
    public function new_comment(Request $request,Article $article)
    {
        $comment = new Comment();
        $comment->setArticle($article);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('comment',[
                'id'=> $article->getId(),
            ]);
        }

        return $this->render('main/new-comment.html.twig', [
            'commentForm' => $form->createView(),
            'article' => $article,
        ]);
    }
}

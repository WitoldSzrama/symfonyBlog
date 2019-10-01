<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Tags;
use App\Entity\User;
use App\Form\TagType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/new-article", name="new_article")
     */
    public function new_article(Request $request)
    {
        $article = new Article();
        $article->setAuthor($this->getUser());

        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('main');
        }

        return $this->render('admin/new-article.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit-article/{id}", name="edit_article")
     */
    public function edit_article(Request $request,Article $article)
    {   
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('main');
        }

        return $this->render('admin/new-article.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new-tag/{id}", name="tag")
     */
    public function new_tag(Request $request,Article $article)
    {
        $tag = new Tags();
        $tag->addArticle($article);
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('main');
        }

        return $this->render('admin/new-tag.html.twig', [
            'tagForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list", name="list")
     */
    public function list(ArticleRepository $arcRep)
    {
        $article= $arcRep->findAll();
        return $this->render('admin/list.html.twig', [
            'articles'=> $article,
        ]);
    }

    /**
     * @Route("/publish/{id}", name="publish")
     */
    public function publish(Article $article)
    {
        $article->setPublished(!($article->getPublished()));
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($article);
        $entityManager->flush();

        return $this->redirectToRoute('list');
    }


    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Article $article)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/users-list", name="users_list")
     */
    public function users_list(UserRepository $usrRep)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users= $usrRep->findAll();
        return $this->render('admin/users-list.html.twig', [
            'users'=> $users,
        ]);
    }

    /**
     * @Route("/admin-role/{id}", name="admin_role")
     */
    public function admin_role(User $user)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if($user->getRoles()[0]=="ROLE_ADMIN")
        {
            $user->setRoles([]);
        }
        else
        {
            $user->setRoles(["ROLE_ADMIN"]);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('users_list');
    }

    /**
     * @Route("/detail-article/{id}", name="detail_article")
     */
    public function detail_article(Article $article)
    {
        return $this->render('admin/detail-article.html.twig', [
            'article'=> $article,
        ]);
    }
}

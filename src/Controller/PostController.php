<?php

namespace App\Controller;

use App\Entity\Post;

use App\Form\PostType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    /**
     * @Route("/post/new", name="new_post")
     * Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newPost(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $post->setAuthor($user->getID());
            $post->setDateCreated(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('feed');
        }

        return $this->render(
            'post/test.html.twig',
            array(
                'form' => $form->createview(),
            )
        );
    }

    /**
     * @Route("/post/edit/{id}", name="edit_post")
     * Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editPost(Request $request, $id)
    {

        $post = new Post();
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('feed');
        }

        return $this->render(
            'post/test.html.twig',
            array(
                'form' => $form->createview(),
            )
        );

    }
}

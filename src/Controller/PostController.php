<?php

namespace App\Controller;

use App\Entity\Post;

use App\Form\PostType;
use App\Service\ImgHandler;
use App\Service\PostLoader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PostController extends AbstractController
{

    /**
     * @Route("/post/new", name="new_post")
     * Method({"GET", "POST"})
     * @param Request $request
     * @param ImgHandler $imgHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function newPost(Request $request, ImgHandler $imgHandler)
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

            $post->setPath($imgHandler->uploadImage($user, $post));

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
    public function editPost(Request $request,PostLoader $postLoader, $id)
    {
        $post = new Post();
//        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        $post = $post = $postLoader->LoadSinglePost($id);

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


    /**
     * @Route("/post/delete/{id}")
     * Method({"DELETE"})
     * @param Request $request
     * @param $id
     */
    public function delete(Request $request, PostLoader $postLoader, $id)
    {
//        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        $post = $postLoader->LoadSinglePost($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();
        $response = new Response();
        $response->send();
    }



//TODO Come back and implement the like system properly
    /**
     * @Route("/post/{id}/like", name="post_toggle_like", methods={"POST"})
     * @param Post $post
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function togglePostLike(Post $post, EntityManagerInterface $em)
    {

        $post->setLikeCount($post->getLikeCount() + 1);
        $em->flush();

        return new JsonResponse(['likes' => $post->getLikeCount()]);
    }
}

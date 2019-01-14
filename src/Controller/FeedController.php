<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{

    /**
     * @Route("/", name="welcome_page")
     */
    public function index()
    {
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('feed');
        }

        return $this->render('feed/index.html.twig');
    }

    /**
     * @Route("/feed", name="feed")
     */
    public function feed()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findByNot('author', $user->getId());

        return $this->render(
            'feed/feed.html.twig',
            array
            (
                'posts' => $posts,
            )
        );
    }

    /**
     * @Route("/profile/{id}", name="profile")
     * @param $id
     * @return Response
     */
    public function userProfiles($id)
    {

        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy(
                ['author' => $id]
            );

        //Gets the user object
        //@TODO Probably a better way of doing this, maybe load user in a service
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(
                ['id' => $id]
            );


        return $this->render(
            'feed/profile.html.twig',
            array
            (
                //@TODO Am aware that this passes the hashed password, needs refactored
                'users' => $user,
                'posts' => $posts,
            )
        );

    }

    /**
     * @Route("/search", name="search")
     * @return Response
     */
    public function searchResults()
    {

        $filter = $_POST['search_criteria'];

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAllThatInclude('username', $filter);


        return $this->render(
            'feed/search.html.twig',
            array
            (
                'users' => $users,
            )
        );
    }

}

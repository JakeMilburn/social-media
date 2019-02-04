<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


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
        //@TODO Probably a better way of doing this, maybe load user in a service rather than constantly in Controllers
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(
                ['id' => $id]
            );


        $friendStatus = null;

        //Check that the user is logged in
        if($this->getUser()) {
            //Load the current user and get their UID
            $currentUserId = $this->getUser()->getId();

            //Checking the status of a friend request
            if ($currentUserId == $user[0]->getId()) {
                $friendStatus = 'my profile';
            } elseif (array_search($currentUserId, array_column($user[0]->getFriends(), '0')) !== false) {
                $friendStatus = 'friend';
            } elseif (array_search($currentUserId, array_column($user[0]->getReceivedRequests(), '0')) !== false) {
                $friendStatus = 'requested';
            } elseif (array_search($currentUserId, array_column($user[0]->getSentRequests(), '0')) !== false) {
                $friendStatus = 'received';
            } else {
                $friendStatus = null;
            }
        }


        return $this->render(
            'feed/profile.html.twig',
            array
            (
                //@TODO Am aware that this passes the hashed password, probably not good / avoidable
                'users' => $user,
                'friendStatus' => $friendStatus,
                'posts' => $posts,
            )
        );

    }


    /**
     * @Route("/profile/edit/{id}", name="edit_profile")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function editProfile(User $user, Request $request)
    {

        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }

        return $this->render(
            'feed/edit-profile.html.twig',
            array(
                'form' => $form->createview(),
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

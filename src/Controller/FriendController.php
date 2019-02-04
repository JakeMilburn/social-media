<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class FriendController extends AbstractController
{
    /**
     * @Route("/friendrequest/{id}/send", name="friendrequest_send", methods={"POST"})
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function sendRequest(User $user, EntityManagerInterface $em)
    {
        $requestSender = $this->getUser();

        $user->addReceivedRequests([$requestSender->getId()]);
        $requestSender->addSentRequests([$user->getId()]);
        $em->flush();

        return new JsonResponse('great success');
    }

    /**
     * @Route("/friendrequest/{id}/check", name="friendrequest_check", methods={"POST"})
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function checkRequests(User $user, EntityManagerInterface $em)
    {

        $requests = $user->getReceivedRequests();

        $users = [];
        foreach ($requests as $request) {
            $test = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(
                    ['id' => $request]
                );

            if ($test[0]->getPath() == 'default-pp.png') {
                $path = 'css/files/system-images/'.$test[0]->getPath();
            } else {
                $path = 'css/files'.$test[0]->getUsername().'/'.$test[0]->getPath();
            }

            $html = '<h2><a href="/social-media/public/profile/'.$test[0]->getId().'">'.$test[0]->getUsername(
                ).'</a></h2><img src="'.$path.'">';
            $html .= '<a class="accept-request" href="/social-media/public/friendrequest/accept/'.$test[0]->getId(
                ).'">Add</a>';
            $html .= '<a class="reject-request" href="/social-media/public/friendrequest/reject/'.$test[0]->getId(
                ).'">Delete</a>';
            array_push($users, $html);
        }

        return new JsonResponse(['requests' => $users]);
    }

    /**
     * @Route("/friendrequest/accept/{id}", name="friendrequest_accept", methods={"POST"})
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function acceptRequest(User $user, EntityManagerInterface $em)
    {

        //ParamConverter loads the user object for the user who has made the request($user)
        //We then load the current user who is accepting the request($currentUser)
        $currentUser = $this->getUser();

        $user->addFriends([$currentUser->getId()]);
        $currentUser->addFriends([$user->getId()]);

        //Removes the received request from the current user
        if (($key = array_search($user->getId(), array_column($currentUser->getReceivedRequests(), '0'))) !== false) {
            $currentUser->removeReceivedRequests($key);
        }
        //Removes the sent request from the user who is being added as a friend
        if (($key = array_search($currentUser->getId(), array_column($user->getSentRequests(), '0'))) !== false) {
            $user->removeSentRequests($key);
        }

        $em->flush();

        return new JsonResponse('success');

    }

    /**
     * @Route("/friendrequest/reject/{id}", name="friendrequest_reject", methods={"POST"})
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function rejectRequest(User $user, EntityManagerInterface $em)
    {

        //ParamConverter loads the user object for the user who has made the request($user)
        //We then load the current user who is accepting the request($currentUser)
        $currentUser = $this->getUser();

        //Removes the received request from the current user
        if (($key = array_search($user->getId(), array_column($currentUser->getSentRequests(), '0'))) !== false) {
            $currentUser->removeSentRequests($key);
        }
        //Removes the sent request from the user who is being added as a friend
        if (($key = array_search($currentUser->getId(), array_column($user->getReceivedRequests(), '0'))) !== false) {
            $user->removeReceivedRequests($key);
        }

        $em->flush();

        return new JsonResponse('great success');
    }

    /**
     * @Route("/friendrequest/remove/{id}", name="friendrequest_remove", methods={"POST"})
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function removeFriend(User $user, EntityManagerInterface $em)
    {

        //ParamConverter loads the user object for the user who has made the request($user)
        //We then load the current user who is accepting the request($currentUser)
        $currentUser = $this->getUser();

        //Removes the received request from the current user
        if (($key = array_search($user->getId(), array_column($currentUser->getFriends(), '0'))) !== false) {
            $currentUser->removeFriends($key);
        }
        //Removes the sent request from the user who is being added as a friend
        if (($key = array_search($currentUser->getId(), array_column($user->getFriends(), '0'))) !== false) {
            $user->removeFriends($key);
        }

        $em->flush();

        return new JsonResponse('great success');
    }
}
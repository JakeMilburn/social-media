<?php

namespace App\Service;

use App\Entity\Post;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostLoader
{

    private $user;
    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, ManagerRegistry $em)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->em = $em;
    }

    public function FeedPosts()
    {

        $user = $this->user;

        $posts = $this->em
            ->getRepository(Post::class)
            ->findByNot('author', $user->getId());

        return $posts;
    }

    public function ProfilePosts($id)
    {
        $posts = $this->em
            ->getRepository(Post::class)
            ->findBy(
                ['author' => $id]
            );

        return $posts;
    }

    public function LoadSinglePost($id)
    {
        $post = $this->em
            ->getRepository(Post::class)
            ->find($id);

        return $post;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="array")
     */
    private $friends = [];

    /**
     * @ORM\Column(type="array")
     */
    private $sent_requests = [];

    /**
     * @ORM\Column(type="array")
     */
    private $received_requests = [];

    /**
     * @return mixed
     */
    public function getSentRequests()
    {
        return $this->sent_requests;
    }

    /**
     * @param mixed $sent_requests
     */
    public function setSentRequests($sent_requests): void
    {
        $this->sent_requests = $sent_requests;
    }

    /**
     * @param $sent_requests
     */
    public function addSentRequests($sent_requests)
    {
        array_push($this->sent_requests, $sent_requests);
    }

    /**
     * @param $sent_requests
     */
    public function removeSentRequests($sent_requests)
    {
        unset($this->sent_requests[$sent_requests]);
    }

    /**
     * @return mixed
     */
    public function getReceivedRequests()
    {
        return $this->received_requests;
    }

    /**
     * @param mixed $received_requests
     */
    public function setReceivedRequests($received_requests): void
    {
        $this->received_requests = $received_requests;
    }

    /**
     * @param $received_requests
     */
    public function addReceivedRequests($received_requests)
    {
        array_push($this->received_requests, $received_requests);
    }

    /**
     * @param $received_requests
     */
    public function removeReceivedRequests($received_requests)
    {
        unset($this->received_requests[$received_requests]);
    }

    /**
     * @return mixed
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @param mixed $friends
     */
    public function setFriends($friends): void
    {
        $this->friends = $friends;
    }

    /**
     * @param $friends
     */
    public function addFriends($friends)
    {
        array_push($this->friends, $friends);
    }

    /**
     * @param $friends
     */
    public function removeFriends($friends)
    {
        unset($this->friends[$friends]);
    }

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $profilePicture;

    public function setProfilePicture(UploadedFile $file = null)
    {
        $this->profilePicture = $file;
    }

    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        return $roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function resetRoles()
    {
        $this->roles = [];
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->username,
                $this->email,
                $this->password,
            ]
        );
    }

    public function unserialize($string)
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password
            ) = unserialize($string, ['allowed_classes' => false]);
    }
}

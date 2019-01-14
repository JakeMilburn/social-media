<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;
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
     * @ORM\Column(type="string", length=191)
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];


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

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../public/css/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'files';
    }

    public function uploadImage()
    {
        $fileSystem = new Filesystem();
        $fileSystem->mkdir('css/files/' . $this->getUsername());
        $this->getProfilePicture()->move(
            $this->getUploadRootDir() . '/' . $this->getUsername(),
            $this->getProfilePicture()->getClientOriginalName()
        );

        $fileName = $this->getProfilePicture()->getClientOriginalName();
        $this->path = $fileName;

        $this->profilePicture = null;
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

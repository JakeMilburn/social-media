<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class ImgHandler
{

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../public/css/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'files';
    }

    public function uploadImage($user, $post = null)
    {
        $fileSystem = new Filesystem();

        //Uploading a profile picture
        if ($post === null) {
            $fileSystem->mkdir('css/files/'.$user->getUsername());
            $user->getProfilePicture()->move(
                $this->getUploadRootDir().'/'.$user->getUsername(),
                $user->getProfilePicture()->getClientOriginalName()
            );

            $fileName = $user->profilePicture->getClientOriginalName();

            return $fileName;
        }

        //Uploading an image with a post
        if ($post->getPath() == null) {
            return;
        }
        $fileSystem->mkdir('css/files/'.$user->getUsername().'/posts');
        $post->getImages()->move(
            $this->getUploadRootDir().'/'.$user->getUsername().'/posts',
            $post->getImages()->getClientOriginalName()
        );

        $fileName = $post->images->getClientOriginalName();

        return $fileName;
    }

}

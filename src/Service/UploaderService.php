<?php

namespace App\Service;

use App\Entity\Upload;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class UploaderService
{
    /**
     * Create and return an uploaded file
     *
     * @param File $file
     * @param User|null $uploader
     * @return Upload
     */
    static function upload(File $file, ?User $uploader): Upload
    {
        $upload = new Upload();
        $upload
            ->setFile($file)
            ->setUploader($uploader);

        return $upload;
    }
}
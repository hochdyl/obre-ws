<?php

namespace App\Controller;

use App\Entity\Upload;
use App\Exceptions\ObreatlasExceptions;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/uploads', name: 'uploads')]
class UploadController extends BaseController
{
    /**
     * @throws Exception
     */
    #[Route('/upload', name: 'upload', methods: 'POST')]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        $file = $request->files->get('file');
        if (!$file) {
            throw new Exception(ObreatlasExceptions::NO_FILE);
        }

        $upload = new Upload();
        $upload
            ->setFile($file)
            ->setUploader($user);

        $em->persist($upload);
        $em->flush();

        return self::response($upload, Response::HTTP_CREATED, [], [
            'groups' => ['upload']
        ]);
    }
}

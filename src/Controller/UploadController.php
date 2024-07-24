<?php

namespace App\Controller;

use App\Entity\Upload;
use App\Exceptions\ObreatlasExceptions;
use App\Service\UploaderService;
use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/uploads', name: 'uploads')]
class UploadController extends BaseController
{
    /**
     * @throws Exception
     */
    #[Route('/upload', name: 'upload', methods: 'POST')]
    public function index(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
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

        $violations = $validator->validate($upload);
        $errors = ValidationService::getViolations($violations);

        if (count($errors) > 0) {
            return self::response($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($upload);
        $em->flush();

        return self::response($upload, Response::HTTP_CREATED, [], [
            'groups' => ['upload']
        ]);
    }
}

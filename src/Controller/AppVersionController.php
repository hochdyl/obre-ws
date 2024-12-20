<?php

namespace App\Controller;

use App\Repository\AppVersionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app-versions', name: 'appVersion')]
class AppVersionController extends BaseController
{
    #[Route(name: 'getAll', methods: 'GET')]
    public function getAll(AppVersionRepository $appVersionRepository): JsonResponse
    {
        $appVersions = $appVersionRepository->findBy([], ['id' => 'DESC']);

        return self::response($appVersions, Response::HTTP_OK, [], [
            'groups' => ['appVersion']
        ]);
    }
}

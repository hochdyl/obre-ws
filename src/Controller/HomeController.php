<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
    #[Route('/', name: 'index')]
    public function index(): JsonResponse
    {
        return self::response('Welcome to obre.io api');
    }
}

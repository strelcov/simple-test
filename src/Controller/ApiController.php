<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    public function createBook(Request $request): JsonResponse {
        return new JsonResponse(['ya' => '1']);
    }

    public function createAuthor(Request $request): JsonResponse {
        return new JsonResponse(['ya' => '1']);
    }

    public function bookSearch(Request $request): JsonResponse {
        return new JsonResponse(['ya' => '1']);
    }

    public function book($id, $lang, Request $request): JsonResponse {
        return new JsonResponse(['ya' => '1']);
    }
}

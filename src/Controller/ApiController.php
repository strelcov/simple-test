<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Model\BookListResponse;
use App\Service\ResponseManager;
use App\Service\SerializeManager;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    private EntityManagerInterface $em;
    private SerializeManager $serializeManager;
    private ValidatorInterface $validator;
    private ResponseManager $responseManager;

    public function __construct(
        EntityManagerInterface $em,
        SerializeManager $serializeManager,
        ValidatorInterface $validator,
        ResponseManager $responseManager
    ) {
        $this->em = $em;
        $this->serializeManager = $serializeManager;
        $this->validator = $validator;
        $this->responseManager = $responseManager;
    }

    public function createBook(Request $request): JsonResponse
    {
        try {
            /** @var Book $book */
            $book = $this->serializeManager->deserializeEntityFromJson(
                $request->getContent(),
                Book::class,
                ['set']
            );
        } catch (NotEncodableValueException|\RuntimeException $e) {
            return $this->responseManager->jsonResponse(
                ['success' => false, 'errorMsg' => 'Invalid request data'],
                400
            );
        }

        $errors = $this->validator->validate($book);
        if ($errors->count() > 0) {
            return $this->responseManager->invalidJsonResponse('Validation error', $errors);
        }

        $this->em->persist($book);
        $this->em->flush();

        $serializeBook = $this->serializeManager->toArray($book, ['get']);

        return $this->responseManager->jsonResponse([
            'success' => true,
            'book' => $serializeBook,
        ], 200);
    }

    public function createAuthor(Request $request): JsonResponse
    {
        try {
            $author = $this->serializeManager->deserializeEntityFromJson(
                $request->getContent(),
                Author::class,
                ['set']
            );
        } catch (NotEncodableValueException|\RuntimeException $e) {
            return $this->responseManager->jsonResponse(
                ['success' => false, 'errorMsg' => 'Invalid request data'],
                400
            );
        }

        $errors = $this->validator->validate($author);
        if ($errors->count() > 0) {
            return $this->responseManager->invalidJsonResponse('Validation error', $errors);
        }

        $this->em->persist($author);
        $this->em->flush();

        $serializeAuthor = $this->serializeManager->toArray($author, ['get']);

        return $this->responseManager->jsonResponse([
            'success' => true,
            'author' => $serializeAuthor,
        ], 200);
    }

    public function bookSearch(Request $request): JsonResponse
    {
        try {
            $json = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $name = $json['name'];
        } catch (\Exception $e) {
            return $this->responseManager->jsonResponse(
                ['success' => false, 'errorMsg' => 'Can not find request parameter Name in json request'],
                400
            );
        }

        $books = $this->em->getRepository(Book::class)->findBooksByName($name);
        $bookResponse = $this->serializeManager->toArray(new BookListResponse($books), ['get']);

        return $this->responseManager->jsonResponse($bookResponse, 200);
    }

    public function book(int $id, string $lang): JsonResponse
    {
        $book = $this->em->find(Book::class, $id);
        if (null === $book) {
            return $this->responseManager->notFoundResponse();
        }
        $transRepository = $this->em->getRepository(Translation::class);
        $translations = $transRepository->findTranslations($book);
        if (empty($translations[$lang])) {
            return $this->responseManager->notFoundResponse("Record for '$lang' locale not found");
        }
        $book->setName($translations[$lang]['name']);

        $serializeBook = $this->serializeManager->toArray($book, ['get']);

        return $this->responseManager->jsonResponse([
            'success' => true,
            'book' => $serializeBook,
        ], 200);
    }
}

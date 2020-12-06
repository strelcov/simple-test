<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Translation;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function createBook(Request $request): JsonResponse {
        $name = $request->request->get('name');
        if (null === $name) {
            return new JsonResponse(['success' => false, 'message' => 'Parameter name not found'], 400);
        }

        $book = (new Book())->setName($name);
        $errors = $this->validator->validate($book);
        if ($errors->count() > 0) {
            //Не стал отображать ошибки валидации для экономии времени
            return new JsonResponse(['success' => false, 'message' => 'Validation error'], 400);
        }

        $serializeBook = $this->serializer->toArray(
            $book,
            SerializationContext::create()->setGroups(['get'])
        );

        return new jsonResponse([
            'success' => true,
            'book' => $serializeBook,
        ], 200);
    }

    public function createAuthor(Request $request): JsonResponse {
        return new JsonResponse(['stub' => '1']);
    }

    public function bookSearch(Request $request): JsonResponse {
        return new JsonResponse(['stub' => '1']);
    }

    public function book(int $id, string $lang): JsonResponse {
        $book = $this->em->find(Book::class, $id);
        if (null === $book) {
            return new JsonResponse(['success' => false, 'message' => 'Not found'], 404);
        }
        $transRepository = $this->em->getRepository(Translation::class);
        $translations = $transRepository->findTranslations($book);
        if (empty($translations[$lang])) {
            return new JsonResponse(['success' => false, 'message' => 'Not found'], 404);
        }
        $book->setName($translations[$lang]['name']);

        $serializeBook = $this->serializer->toArray(
            $book,
            SerializationContext::create()->setGroups(['get'])
        );

        return new jsonResponse([
            'success' => true,
            'book' => $serializeBook,
        ], 200);
    }
}

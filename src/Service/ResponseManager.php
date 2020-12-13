<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;
use function Symfony\Component\String\u;

class ResponseManager
{
    public function jsonResponse(array $data, $statusCode, $headers = []): JsonResponse
    {
        // Энкодим вручную, т.к. в JsonResponse данные не экранируются
        return new JsonResponse(json_encode($data, JSON_PRETTY_PRINT), $statusCode, $headers, true);
    }

    public function invalidJsonResponse(string $errorMsg, $errors = null, $statusCode = 400): JsonResponse
    {
        $responseErrors = $this->getErrorsArray($errors);

        // Энкодим вручную, т.к. в JsonResponse данные не экранируются
        $data = json_encode([
            'success' => false,
            'errorMsg' => $errorMsg,
            'errors' => $responseErrors,
        ], JSON_PRETTY_PRINT);

        return new JsonResponse($data, $statusCode, [], true);
    }

    public function invalidNamedFieldsJsonResponse(string $errorMsg, $errors = null, $statusCode = 400): JsonResponse
    {
        $responseErrors = $this->getErrorsArray($errors, true);

        // Энкодим вручную, т.к. в JsonResponse данные не экранируются
        $data = json_encode([
            'success' => false,
            'errorMsg' => $errorMsg,
            'errors' => $responseErrors,
        ], JSON_PRETTY_PRINT);

        return new JsonResponse($data, $statusCode, [], true);
    }

    public function notFoundResponse($customMsg = 'Not found error'): JsonResponse
    {
        return $this->jsonResponse(
            [
                'success' => false,
                'errorMsg' => $customMsg,
            ],
            404
        );
    }

    private function getErrorsArray($errors, $keyIsProperty = false): array
    {
        $responseErrors = [];

        if (null !== $errors) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                if (!($error instanceof ConstraintViolation)) {
                    continue;
                }
                $property = $error->getPropertyPath();
                if ($keyIsProperty) { // В качестве ключей - названия полей.
                    $errorMessage = u($error->getMessage())->title();
                    if ($property && $fieldName = $this->getFieldNameByPath($property)) {
                        $responseErrors[$fieldName] = $errorMessage;
                    } else {
                        $responseErrors[] = $errorMessage;
                    }
                } else { // Обычный массив.
                    $errorMessage = $error->getMessage();
                    if ($property) {
                        $fieldName = $this->getFieldNameByPath($property);
                        $errorMessage = $fieldName ? $fieldName . ': ' . $errorMessage : $errorMessage;
                    }
                    $responseErrors[] = u($errorMessage)->title();
                }
            }
        }

        return $responseErrors;
    }

    private function getFieldNameByPath(string $path): string
    {
        $pathName = $path;
        $paths = explode('.', $path);
        if (count($paths) > 1) {
            $pathName = $paths[count($paths) - 1];
        }

        return $pathName;
    }
}

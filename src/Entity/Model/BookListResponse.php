<?php

declare(strict_types=1);

namespace App\Entity\Model;

use JMS\Serializer\Annotation as Serializer;

class BookListResponse
{
    /**
     * response.success
     *
     * @Serializer\Groups({"get"})
     * @Serializer\Type("boolean")
     */
    public $success = true;

    /**
     * @Serializer\Groups({"get"})
     * @Serializer\Type("array<App\Entity\Book>")
     */
    public $books;

    public function __construct(array $books)
    {
        $this->books = $books;
    }
}

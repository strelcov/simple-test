<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testBookCreate(): void
    {
        $book = new Book();
        $author1 = new Author('author 1');
        $author2 = new Author('author 2');
        $book->setName('book name')
            ->setAuthors(new ArrayCollection([$author1, $author2]));

        $this->assertEquals('book name', $book->getName());
        $this->assertEquals(2, $book->getAuthors()->count());
    }
}
<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Gedmo\Translatable\Entity\Translation;

class AppFixtures extends Fixture
{
    public const BATCH_SIZE = 200;
    public const COUNT = 10000;
    public const COUNT_AUTHORS_FOR_BOOK_LIMIT = 3;

    public function load(ObjectManager $em): void
    {
        $tranRepository = $em->getRepository(Translation::class);
        $fakerRu = Factory::create('ru_RU');
        $fakerEn = Factory::create('en_US');

        $authors = new ArrayCollection();
        for ($i = 1; $i <= self::COUNT; ++$i) {
            $author = new Author($fakerRu->name());
            $em->persist($author);
            $authors->add($author);

            $book = (new Book())->setAuthors($authors);
            $tranRepository
                ->translate($book, 'name', 'ru', $fakerRu->company)
                ->translate($book, 'name', 'en', $fakerEn->company);

            $em->persist($book);

            if (0 === ($i % self::BATCH_SIZE)) {
                $em->flush();
                $em->clear();
                $authors->clear();
            }

            if (self::COUNT_AUTHORS_FOR_BOOK_LIMIT <= $authors->count()) {
                $authors = new ArrayCollection();
            }
        }

        $em->flush();
        $em->clear();
    }
}

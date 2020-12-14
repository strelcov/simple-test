<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use function Symfony\Component\String\u;

class BookRepository extends EntityRepository
{
    const BOOK_BY_NAME = 'book_by_name';
    const MAX_COUNT = 10;

    public function findBooksByName($name)
    {
        $qb = $this->createQueryBuilder('b');

        return $qb
            ->where("LOWER(b.name) LIKE :name")
            ->orderBy('b.id')
            ->setParameter('name', u("%$name%")->lower())
            ->getQuery()
            ->enableResultCache(3600 * 24, self::BOOK_BY_NAME)
            ->setMaxResults(self::MAX_COUNT)
            ->getResult();
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\Table(
 *     name="i_book",
 *     indexes={
 *         @ORM\Index(name="i_book_name_ind", columns={"name"})
 *     }
 * )
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @Serializer\Groups({"get"})
     * @Serializer\Type("int")
     */
    private int $id;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Serializer\Groups({"get", "set"})
     * @Serializer\Type("string")
     */
    private string $name;

    /**
     * @ORM\ManyToMany(targetEntity="Author", cascade={"persist"})
     * @ORM\JoinTable(name="i_author_book",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     * @Serializer\Groups({"get", "set"})
     * @Serializer\Accessor(getter="getAuthorsArray", setter="setAuthorsArray")
     * @Serializer\Type("array<App\Entity\Author>")
     */
    protected ?Collection $authors = null;

    /**
     * @Gedmo\Locale
     */
    private ?string $locale = null;

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('name', new Assert\Length(['max' => 255]));
        $metadata->addPropertyConstraint('authors', new Assert\Valid());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthors(): Collection
    {
        return $this->authors ?? new ArrayCollection();
    }

    public function setAuthors(Collection $authors): self
    {
        $this->authors = $authors;

        return $this;
    }

    public function getAuthorsArray(): array
    {
        return null === $this->authors ? [] : $this->authors->toArray();
    }

    public function setAuthorsArray(array $authors): self
    {
        $this->authors = new ArrayCollection($authors);

        return $this;
    }

    public function setTranslatableLocale(string $locale)
    {
        $this->locale = $locale;

        return $this;
    }
}

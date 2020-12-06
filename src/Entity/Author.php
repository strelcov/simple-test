<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 * @ORM\Table(
 *     name="i_author"
 * )
 */
class Author
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @Serializer\Groups({"get"})
     * @Serializer\Type("string")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Serializer\Groups({"get", "set"})
     * @Serializer\Type("string")
     */
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank([
            'message' => 'Поле должно быть заполнено',
        ]));
        $metadata->addPropertyConstraint('name', new Assert\Length([
            'max' => 255,
            'maxMessage' => 'Максимальная длина поля 255 символов',
        ]));
    }

    public function __toString(): string {
        return $this->name;
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
}

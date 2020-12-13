<?php

declare(strict_types=1);

namespace App\Service;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class SerializeManager
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function deserializeEntityFromJson(string $json, string $entityClassName, array $groups = [])
    {
        return $this->serializer->deserialize(
            $json,
            $entityClassName,
            'json',
            DeserializationContext::create()->setGroups($groups)
        );
    }

    public function toArray($entity, array $groups = []): array
    {
        return $this->serializer->toArray(
            $entity,
            SerializationContext::create()->setGroups($groups)
        );
    }
}

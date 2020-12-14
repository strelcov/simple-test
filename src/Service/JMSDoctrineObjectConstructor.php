<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Construction\ObjectConstructorInterface;

class JMSDoctrineObjectConstructor implements ObjectConstructorInterface
{
    private ManagerRegistry $managerRegistry;
    private ObjectConstructorInterface $fallbackConstructor;

    public function __construct(ManagerRegistry $managerRegistry, ObjectConstructorInterface $fallbackConstructor)
    {
        $this->managerRegistry = $managerRegistry;
        $this->fallbackConstructor = $fallbackConstructor;
    }

    public function construct(
        DeserializationVisitorInterface $visitor,
        ClassMetadata $metadata,
        $data,
        array $type,
        DeserializationContext $context
    ): ?object {
        $objectManager = $this->managerRegistry->getManagerForClass($metadata->name);

        if (!$objectManager) {
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        $classMetadataFactory = $objectManager->getMetadataFactory();
        if ($classMetadataFactory->isTransient($metadata->name)) {
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        if (!is_array($data)) {
            return $objectManager->getReference($metadata->name, $data);
        }

        $classMetadata = $objectManager->getClassMetadata($metadata->name);
        $identifierList = [];

        foreach ($classMetadata->getIdentifierFieldNames() as $name) {
            if (!array_key_exists($name, $data)) {
                return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
            }

            $identifierList[$name] = $data[$name];
        }

        if (array_key_exists('id', $identifierList) && $identifierList['id']) {
            $object = $objectManager->find($metadata->name, $identifierList);
            if (null === $object) {
                throw new \RuntimeException("Object with id {$identifierList['id']} not found");
            }
        } else {
            $object = new $metadata->name;
        }

        $objectManager->initializeObject($object);

        return $object;
    }
}
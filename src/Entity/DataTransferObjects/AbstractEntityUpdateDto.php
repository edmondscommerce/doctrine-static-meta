<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use Ramsey\Uuid\UuidInterface;

class AbstractEntityUpdateDto implements DataTransferObjectInterface
{
    /**
     * @var string
     */
    private static string $entityFqn;
    /**
     * @var UuidInterface
     */
    private UuidInterface $id;

    public function __construct(string $entityFqn, UuidInterface $id)
    {
        self::$entityFqn = $entityFqn;
        $this->id        = $id;
    }

    public static function getEntityFqn(): string
    {
        return self::$entityFqn;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): AbstractEntityUpdateDto
    {
        $this->id = $id;

        return $this;
    }
}

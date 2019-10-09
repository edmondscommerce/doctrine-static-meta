<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Ramsey\Uuid\UuidInterface;

/**
 * This is the root interface implemented by both Entity and DataTransferObject
 */
interface EntityData
{
    public static function getEntityFqn(): string;

    public function getId(): UuidInterface;
}

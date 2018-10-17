<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

interface DataTransferObjectInterface
{
    public static function getEntityFqn(): string;

    public function getId();
}

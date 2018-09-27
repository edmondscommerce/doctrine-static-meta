<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;

interface AlwaysValidInterface
{
    public static function create(
        EntityFactory $factory,
        DataTransferObjectInterface $dto = null
    );

    public function update(DataTransferObjectInterface $dto): void;
}
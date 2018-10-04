<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityDataValidatorInterface;

interface AlwaysValidInterface
{
    public static function create(
        EntityFactoryInterface $factory,
        DataTransferObjectInterface $dto = null
    );

    public function update(DataTransferObjectInterface $dto): void;

    public function injectEntityDataValidator(EntityDataValidatorInterface $entityDataValidator);
}

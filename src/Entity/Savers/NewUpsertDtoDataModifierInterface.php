<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;

interface NewUpsertDtoDataModifierInterface
{
    public function addDataToNewlyCreatedDto(DataTransferObjectInterface $dto): void;
}

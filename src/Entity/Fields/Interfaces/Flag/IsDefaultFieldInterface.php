<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag;

interface IsDefaultFieldInterface
{
    public const PROP_IS_DEFAULT = 'isDefault';

    public function getIsDefault(): int;

    public function setIsDefault(int $isDefault);
}

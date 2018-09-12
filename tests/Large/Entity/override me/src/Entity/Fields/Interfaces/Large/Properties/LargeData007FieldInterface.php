<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData007FieldInterface
{
    public const PROP_LARGE_DATA007 = 'largeData007';

    public const DEFAULT_LARGE_DATA007 = null;

    public function isLargeData007(): ?bool;

    public function setLargeData007(?bool $largeData007);
}

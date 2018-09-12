<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData032FieldInterface
{
    public const PROP_LARGE_DATA032 = 'largeData032';

    public const DEFAULT_LARGE_DATA032 = null;

    public function isLargeData032(): ?bool;

    public function setLargeData032(?bool $largeData032);
}

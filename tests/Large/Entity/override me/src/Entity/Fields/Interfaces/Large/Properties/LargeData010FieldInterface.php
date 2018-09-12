<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData010FieldInterface
{
    public const PROP_LARGE_DATA010 = 'largeData010';

    public const DEFAULT_LARGE_DATA010 = null;

    public function isLargeData010(): ?bool;

    public function setLargeData010(?bool $largeData010);
}

<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData033FieldInterface
{
    public const PROP_LARGE_DATA033 = 'largeData033';

    public const DEFAULT_LARGE_DATA033 = null;

    public function isLargeData033(): ?bool;

    public function setLargeData033(?bool $largeData033);
}

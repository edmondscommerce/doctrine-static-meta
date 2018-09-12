<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData003FieldInterface
{
    public const PROP_LARGE_DATA003 = 'largeData003';

    public const DEFAULT_LARGE_DATA003 = null;

    public function isLargeData003(): ?bool;

    public function setLargeData003(?bool $largeData003);
}

<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData030FieldInterface
{
    public const PROP_LARGE_DATA030 = 'largeData030';

    public const DEFAULT_LARGE_DATA030 = null;

    public function isLargeData030(): ?bool;

    public function setLargeData030(?bool $largeData030);
}

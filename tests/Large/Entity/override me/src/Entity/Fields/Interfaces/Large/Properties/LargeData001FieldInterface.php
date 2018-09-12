<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData001FieldInterface
{
    public const PROP_LARGE_DATA001 = 'largeData001';

    public const DEFAULT_LARGE_DATA001 = null;

    public function isLargeData001(): ?bool;

    public function setLargeData001(?bool $largeData001);
}

<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData022FieldInterface
{
    public const PROP_LARGE_DATA022 = 'largeData022';

    public const DEFAULT_LARGE_DATA022 = null;

    public function isLargeData022(): ?bool;

    public function setLargeData022(?bool $largeData022);
}

<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData031FieldInterface
{
    public const PROP_LARGE_DATA031 = 'largeData031';

    public const DEFAULT_LARGE_DATA031 = null;

    public function isLargeData031(): ?bool;

    public function setLargeData031(?bool $largeData031);
}

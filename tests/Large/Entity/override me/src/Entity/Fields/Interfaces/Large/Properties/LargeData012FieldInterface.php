<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData012FieldInterface
{
    public const PROP_LARGE_DATA012 = 'largeData012';

    public const DEFAULT_LARGE_DATA012 = null;

    public function isLargeData012(): ?bool;

    public function setLargeData012(?bool $largeData012);
}

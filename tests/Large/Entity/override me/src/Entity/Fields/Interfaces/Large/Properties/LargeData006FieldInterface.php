<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData006FieldInterface
{
    public const PROP_LARGE_DATA006 = 'largeData006';

    public const DEFAULT_LARGE_DATA006 = null;

    public function isLargeData006(): ?bool;

    public function setLargeData006(?bool $largeData006);
}

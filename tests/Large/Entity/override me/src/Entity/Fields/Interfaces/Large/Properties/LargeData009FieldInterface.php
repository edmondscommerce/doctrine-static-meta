<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData009FieldInterface
{
    public const PROP_LARGE_DATA009 = 'largeData009';

    public const DEFAULT_LARGE_DATA009 = null;

    public function isLargeData009(): ?bool;

    public function setLargeData009(?bool $largeData009);
}

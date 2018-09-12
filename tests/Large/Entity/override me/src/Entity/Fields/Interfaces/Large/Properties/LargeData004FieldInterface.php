<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData004FieldInterface
{
    public const PROP_LARGE_DATA004 = 'largeData004';

    public const DEFAULT_LARGE_DATA004 = null;

    public function isLargeData004(): ?bool;

    public function setLargeData004(?bool $largeData004);
}

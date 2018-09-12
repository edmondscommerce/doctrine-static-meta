<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData020FieldInterface
{
    public const PROP_LARGE_DATA020 = 'largeData020';

    public const DEFAULT_LARGE_DATA020 = null;

    public function isLargeData020(): ?bool;

    public function setLargeData020(?bool $largeData020);
}

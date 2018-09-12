<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData019FieldInterface
{
    public const PROP_LARGE_DATA019 = 'largeData019';

    public const DEFAULT_LARGE_DATA019 = null;

    public function isLargeData019(): ?bool;

    public function setLargeData019(?bool $largeData019);
}

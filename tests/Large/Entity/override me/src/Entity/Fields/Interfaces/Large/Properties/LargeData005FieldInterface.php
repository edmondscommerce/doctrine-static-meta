<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData005FieldInterface
{
    public const PROP_LARGE_DATA005 = 'largeData005';

    public const DEFAULT_LARGE_DATA005 = null;

    public function isLargeData005(): ?bool;

    public function setLargeData005(?bool $largeData005);
}

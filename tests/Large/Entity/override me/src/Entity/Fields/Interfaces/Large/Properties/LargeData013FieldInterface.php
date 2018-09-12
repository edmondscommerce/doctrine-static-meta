<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData013FieldInterface
{
    public const PROP_LARGE_DATA013 = 'largeData013';

    public const DEFAULT_LARGE_DATA013 = null;

    public function isLargeData013(): ?bool;

    public function setLargeData013(?bool $largeData013);
}

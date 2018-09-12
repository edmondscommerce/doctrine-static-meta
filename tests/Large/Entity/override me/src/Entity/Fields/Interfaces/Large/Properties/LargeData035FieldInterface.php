<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData035FieldInterface
{
    public const PROP_LARGE_DATA035 = 'largeData035';

    public const DEFAULT_LARGE_DATA035 = null;

    public function isLargeData035(): ?bool;

    public function setLargeData035(?bool $largeData035);
}

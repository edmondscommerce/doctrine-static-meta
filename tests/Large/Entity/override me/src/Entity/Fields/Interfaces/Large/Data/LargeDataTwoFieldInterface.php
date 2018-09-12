<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Data;

interface LargeDataTwoFieldInterface
{
    public const PROP_LARGE_DATA_TWO = 'largeDataTwo';

    public const DEFAULT_LARGE_DATA_TWO = null;

    public function getLargeDataTwo(): ?string;

    public function setLargeDataTwo(?string $largeDataTwo);
}

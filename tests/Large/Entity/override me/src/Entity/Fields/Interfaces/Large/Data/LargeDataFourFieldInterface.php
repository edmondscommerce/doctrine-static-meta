<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Data;

interface LargeDataFourFieldInterface
{
    public const PROP_LARGE_DATA_FOUR = 'largeDataFour';

    public const DEFAULT_LARGE_DATA_FOUR = null;

    public function getLargeDataFour(): ?string;

    public function setLargeDataFour(?string $largeDataFour);
}

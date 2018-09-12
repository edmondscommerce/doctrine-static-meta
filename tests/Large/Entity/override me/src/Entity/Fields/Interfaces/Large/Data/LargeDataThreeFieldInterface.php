<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Data;

interface LargeDataThreeFieldInterface
{
    public const PROP_LARGE_DATA_THREE = 'largeDataThree';

    public const DEFAULT_LARGE_DATA_THREE = null;

    public function getLargeDataThree(): ?string;

    public function setLargeDataThree(?string $largeDataThree);
}

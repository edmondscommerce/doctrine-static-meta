<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Data;

interface LargeDataOneFieldInterface
{
    public const PROP_LARGE_DATA_ONE = 'largeDataOne';

    public const DEFAULT_LARGE_DATA_ONE = null;

    public function getLargeDataOne(): ?string;

    public function setLargeDataOne(?string $largeDataOne);
}

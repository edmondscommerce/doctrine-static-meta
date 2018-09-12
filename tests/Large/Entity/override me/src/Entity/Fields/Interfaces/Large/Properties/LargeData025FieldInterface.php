<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData025FieldInterface
{
    public const PROP_LARGE_DATA025 = 'largeData025';

    public const DEFAULT_LARGE_DATA025 = null;

    public function isLargeData025(): ?bool;

    public function setLargeData025(?bool $largeData025);
}

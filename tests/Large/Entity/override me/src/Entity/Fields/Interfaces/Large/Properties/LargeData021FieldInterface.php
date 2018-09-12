<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData021FieldInterface
{
    public const PROP_LARGE_DATA021 = 'largeData021';

    public const DEFAULT_LARGE_DATA021 = null;

    public function isLargeData021(): ?bool;

    public function setLargeData021(?bool $largeData021);
}

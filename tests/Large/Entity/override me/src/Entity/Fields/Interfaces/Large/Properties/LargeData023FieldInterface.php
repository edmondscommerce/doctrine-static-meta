<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData023FieldInterface
{
    public const PROP_LARGE_DATA023 = 'largeData023';

    public const DEFAULT_LARGE_DATA023 = null;

    public function isLargeData023(): ?bool;

    public function setLargeData023(?bool $largeData023);
}

<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData016FieldInterface
{
    public const PROP_LARGE_DATA016 = 'largeData016';

    public const DEFAULT_LARGE_DATA016 = null;

    public function isLargeData016(): ?bool;

    public function setLargeData016(?bool $largeData016);
}

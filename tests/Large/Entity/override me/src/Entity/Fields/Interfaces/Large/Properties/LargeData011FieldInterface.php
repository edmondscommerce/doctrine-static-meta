<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData011FieldInterface
{
    public const PROP_LARGE_DATA011 = 'largeData011';

    public const DEFAULT_LARGE_DATA011 = null;

    public function isLargeData011(): ?bool;

    public function setLargeData011(?bool $largeData011);
}

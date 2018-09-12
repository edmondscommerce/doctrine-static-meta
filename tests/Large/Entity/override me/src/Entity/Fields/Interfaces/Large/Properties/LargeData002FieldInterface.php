<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData002FieldInterface
{
    public const PROP_LARGE_DATA002 = 'largeData002';

    public const DEFAULT_LARGE_DATA002 = null;

    public function isLargeData002(): ?bool;

    public function setLargeData002(?bool $largeData002);
}

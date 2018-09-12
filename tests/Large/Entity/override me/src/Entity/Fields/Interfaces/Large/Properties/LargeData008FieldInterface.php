<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Large\Properties;

interface LargeData008FieldInterface
{
    public const PROP_LARGE_DATA008 = 'largeData008';

    public const DEFAULT_LARGE_DATA008 = null;

    public function isLargeData008(): ?bool;

    public function setLargeData008(?bool $largeData008);
}

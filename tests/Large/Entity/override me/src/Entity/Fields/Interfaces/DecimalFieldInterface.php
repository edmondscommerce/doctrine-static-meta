<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface DecimalFieldInterface
{
    public const PROP_DECIMAL = 'decimal';

    public const DEFAULT_DECIMAL = null;

    public function getDecimal();

    public function setDecimal($decimal);
}

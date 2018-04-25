<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Attribute;

interface TotalFieldInterface
{
    public const PROP_TOTAL = 'total';

    public function getTotal(): float;

    public function setTotal(float $total);
}

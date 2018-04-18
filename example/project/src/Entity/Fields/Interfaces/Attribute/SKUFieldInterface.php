<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Attribute;

interface SKUFieldInterface
{
    public const PROP_S_K_U = 'sKU';

    public function getSKU(): string;

    public function setSKU(string $sKU);
}

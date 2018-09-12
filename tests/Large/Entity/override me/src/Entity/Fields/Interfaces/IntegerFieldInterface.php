<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface IntegerFieldInterface
{
    public const PROP_INTEGER = 'integer';

    public const DEFAULT_INTEGER = null;

    public function getInteger(): ?int;

    public function setInteger(?int $integer);
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class IsbnFakerData extends AbstractFakerDataProvider
{
    private const FORMATTERS = [
        'isbn10',
        'isbn13',
    ];

    public function __invoke()
    {
        $pseudoProperty = self::FORMATTERS[array_rand(self::FORMATTERS)];

        return $this->generator->unique()->$pseudoProperty;
    }
}

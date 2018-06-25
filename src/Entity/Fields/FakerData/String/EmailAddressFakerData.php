<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class EmailAddressFakerData extends AbstractFakerDataProvider
{
    private const FORMATTERS = [
        'email',
        'companyEmail',
        'freeEmail',
        'safeEmail',
    ];

    public function __invoke()
    {
        $pseudoProperty = self::FORMATTERS[array_rand(self::FORMATTERS)];

        return $this->generator->$pseudoProperty;
    }

}

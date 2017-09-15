<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\ExampleEntities;

use EdmondsCommerce\DoctrineStaticMeta\ExampleEntities\Traits\Relations\Properties\HasAddresses;
use EdmondsCommerce\DoctrineStaticMeta\ExampleEntities\Traits\Relations\Properties\HasPhoneNumbers;
use EdmondsCommerce\DoctrineStaticMeta\Traits\Fields\IdField;
use EdmondsCommerce\DoctrineStaticMeta\Traits\Fields\NameField;
use EdmondsCommerce\DoctrineStaticMeta\Traits\Fields\YearOfBirthField;
use EdmondsCommerce\DoctrineStaticMeta\Traits\UsesPHPMetaData;


class Person
{
    use UsesPHPMetaData;

    use
        IdField,
        YearOfBirthField,
        NameField;

    use
        HasAddresses,
        HasPhoneNumbers;

    /**
     * OVERRIDE
     *
     * @return string
     */
    public static function getPlural(): string
    {
        return 'people';
    }


}

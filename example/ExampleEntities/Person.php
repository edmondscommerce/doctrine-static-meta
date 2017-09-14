<?php declare(strict_types=1);

namespace Edmonds\DoctrineStaticMeta\ExampleEntities;

use Edmonds\DoctrineStaticMeta\ExampleEntities\Traits\Relations\Properties\HasAddresses;
use Edmonds\DoctrineStaticMeta\ExampleEntities\Traits\Relations\Properties\HasPhoneNumbers;
use Edmonds\DoctrineStaticMeta\Traits\Fields\IdField;
use Edmonds\DoctrineStaticMeta\Traits\Fields\NameField;
use Edmonds\DoctrineStaticMeta\Traits\Fields\YearOfBirthField;
use Edmonds\DoctrineStaticMeta\Traits\UsesPHPMetaData;


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

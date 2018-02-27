<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

interface EntityInterface extends
    UsesPHPMetaDataInterface,
    ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface
{

}

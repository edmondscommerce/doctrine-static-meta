<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use JsonSerializable;

interface EntityInterface extends
    EntityData,
    UsesPHPMetaDataInterface,
    ValidatedEntityInterface,
    ImplementNotifyChangeTrackingPolicyInterface,
    AlwaysValidInterface,
    DSM\Fields\Interfaces\PrimaryKey\IdFieldInterface,
    JsonSerializable
{

}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

interface EntityInterface extends
    UsesPHPMetaDataInterface,
    ValidatedEntityInterface,
    DSM\Fields\Interfaces\PrimaryKey\IdFieldInterface,
    ImplementNotifyChangeTrackingPolicyInterface
{
    public static function create(DSM\Factory\EntityFactory $factory, array $values);

    public function update(DataTransferObjectInterface $dto);

    public function getDto(): DataTransferObjectInterface;
}

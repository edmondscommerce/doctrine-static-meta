<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Doctrine\Common\NotifyPropertyChanged;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

interface EntityInterface extends
    UsesPHPMetaDataInterface,
    ValidatedEntityInterface,
    DSM\Fields\Interfaces\PrimaryKey\IdFieldInterface,
    NotifyPropertyChanged
{

}

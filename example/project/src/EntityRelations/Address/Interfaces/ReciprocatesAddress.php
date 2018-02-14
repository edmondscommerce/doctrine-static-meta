<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Address\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Address;

interface ReciprocatesAddress
{
    public function reciprocateRelationOnAddress(Address $address): UsesPHPMetaDataInterface;

    public function removeRelationOnAddress(Address $address): UsesPHPMetaDataInterface;
}

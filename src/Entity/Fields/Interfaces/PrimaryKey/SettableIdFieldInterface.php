<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey;

interface SettableIdFieldInterface extends IdFieldInterface
{
    /**
     * @param int|string $id
     *
     * @return self
     */
    public function setId($id);
}
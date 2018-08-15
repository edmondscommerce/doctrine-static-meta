<?php
namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping;

interface GenericFactoryInterface
{
    public function getEntity(string $className);
}

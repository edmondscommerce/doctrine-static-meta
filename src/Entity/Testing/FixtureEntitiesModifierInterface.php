<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;


interface FixtureEntitiesModifierInterface
{
    /**
     * In this method, you are passed the array of generated Entities
     *
     * You can do things like generate further entities, modify the existing entities, or anything else
     *
     * @param array $entities
     */
    public function modifyEntities(array &$entities): void;
}

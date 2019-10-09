<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

/**
 * This interface should be implemented in order to modify the generated Entity fixtures
 *
 * It has only one public method though of course you can add as many other methods as you want. As standard this
 * public method is called after the Entities are generated with Faker data,but before they are saved
 */
interface FixtureEntitiesModifierInterface
{
    /**
     * In this method, you are passed the array of generated Entities
     *
     * You can do things like generate further entities, modify the existing entities, or anything else
     *
     * @param array|EntityInterface[] $entities
     */
    public function modifyEntities(array &$entities): void;
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class UuidFieldTraitTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class UuidFieldTraitTest extends IdFieldTraitTest
{
    public const    WORK_DIR        = AbstractTest::VAR_PATH .
                                      '/' .
                                      self::TEST_TYPE_LARGE .
                                      '/UuidFieldTraitTest/';
    protected const TEST_FIELD_FQN  = UuidFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;

    protected const UUID_REGEX =
        '/^(\{{0,1}([0-9a-fA-F]){8}-([0-9a-fA-F]){4}-([0-9a-fA-F]){4}-([0-9a-fA-F]){4}-([0-9a-fA-F]){12}\}{0,1})$/i';

    public function generateCode(): void
    {
        $this->getEntityGenerator()
             ->setUseUuidPrimaryKey(true)
             ->generateEntity(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
    }

    protected function validateSavedEntity($entity): void
    {
        $id = $entity->getId();
        self::assertNotEmpty($id);
        self::assertRegExp(self::UUID_REGEX, $id);
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ConfigException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function createEntityWithField(): void
    {
        parent::createEntityWithField();
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function createDatabaseSchema(): void
    {
        parent::createDatabaseSchema();
    }
}

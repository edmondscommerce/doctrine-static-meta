<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

class IsbnFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/IsbnFieldTraitTest/';
    protected const TEST_FIELD_FQN     = IsbnFieldTrait::class;
    protected const TEST_FIELD_PROP    = IsbnFieldInterface::PROP_ISBN;
    protected const TEST_FIELD_DEFAULT = IsbnFieldInterface::DEFAULT_ISBN;

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait
     */
    public function createEntityWithField(): void
    {
        parent::createEntityWithField();
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait
     */
    public function createDatabaseSchema(): void
    {
        parent::createDatabaseSchema();
    }

    /**
     * @large
     * @test
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait
     */
    public function itShouldntAllowAnInvalidIsbn(): void
    {
        $invalidIsbn = 'not an isbn';
        $this->setupCopiedWorkDir();
        $entityFqn = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
        /**
         * @var IsbnFieldInterface $entity
         */
        $entity = new $entityFqn($this->container->get(EntityValidatorFactory::class));
        $this->expectException(ValidationException::class);
        $entity->setIsbn($invalidIsbn);
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

class IsbnFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/IsbnFieldTraitTest/';
    protected const TEST_FIELD_FQN     = IsbnFieldTrait::class;
    protected const TEST_FIELD_PROP    = IsbnFieldInterface::PROP_ISBN;
    protected const TEST_FIELD_DEFAULT = IsbnFieldInterface::DEFAULT_ISBN;

    /**
     * @large
     * @test
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

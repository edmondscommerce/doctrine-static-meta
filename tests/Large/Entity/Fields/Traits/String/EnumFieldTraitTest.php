<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

class EnumFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/EnumFieldTraitTest/';
    protected const TEST_FIELD_FQN     = EnumFieldTrait::class;
    protected const TEST_FIELD_PROP    = EnumFieldInterface::PROP_ENUM;
    protected const TEST_FIELD_DEFAULT = EnumFieldInterface::DEFAULT_ENUM;

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait
     */
    public function createEntityWithField(): void
    {
        parent::createEntityWithField();
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait
     */
    public function createDatabaseSchema(): void
    {
        parent::createDatabaseSchema();
    }
}

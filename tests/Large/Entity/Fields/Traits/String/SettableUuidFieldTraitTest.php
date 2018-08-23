<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\SettableUuidFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\SettableUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

class SettableUuidFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/SettableUuidFieldTraitTest/';
    protected const TEST_FIELD_FQN     = SettableUuidFieldTrait::class;
    protected const TEST_FIELD_PROP    = SettableUuidFieldInterface::PROP_SETTABLE_UUID;
    protected const TEST_FIELD_DEFAULT = SettableUuidFieldInterface::DEFAULT_SETTABLE_UUID;

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\SettableUuidFieldTrait
     */
    public function createEntityWithField(): void
    {
        parent::createEntityWithField();
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\SettableUuidFieldTrait
     */
    public function createDatabaseSchema(): void
    {
        parent::createDatabaseSchema();
    }
}

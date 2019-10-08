<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\SettableUuidFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\SettableUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\SettableUuidFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\SettableUuidFakerData
 */
class SettableUuidFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/SettableUuidFieldTraitTest/';
    protected const TEST_FIELD_FQN     = SettableUuidFieldTrait::class;
    protected const TEST_FIELD_PROP    = SettableUuidFieldInterface::PROP_SETTABLE_UUID;
    protected const TEST_FIELD_DEFAULT = SettableUuidFieldInterface::DEFAULT_SETTABLE_UUID;
    protected const VALID_VALUES       = [
        '123e4567-e89b-12d3-a456-426655440000',
        '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
    ];
    protected const INVALID_VALUES     = [
        'cheese',
        '9999',
    ];
}

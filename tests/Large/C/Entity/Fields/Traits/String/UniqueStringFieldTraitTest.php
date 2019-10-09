<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UniqueStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UniqueStringFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UniqueStringFakerData
 */
class UniqueStringFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/UniqueStringFieldTraitTest/';
    protected const TEST_FIELD_FQN     = UniqueStringFieldTrait::class;
    protected const TEST_FIELD_PROP    = UniqueStringFieldInterface::PROP_UNIQUE_STRING;
    protected const TEST_FIELD_DEFAULT = UniqueStringFieldInterface::DEFAULT_UNIQUE_STRING;
    protected const VALIDATES          = false;
}

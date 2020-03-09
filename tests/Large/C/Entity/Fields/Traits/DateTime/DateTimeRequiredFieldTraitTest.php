<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DateTimeRequiredFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime\DateTimeRequiredFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime\DateTimeRequiredFieldTrait
 */
class DateTimeRequiredFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/DateTimeRequiredFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DateTimeRequiredFieldTrait::class;
    protected const TEST_FIELD_PROP    = DateTimeRequiredFieldInterface::PROP_DATE_TIME_REQUIRED;
    protected const TEST_FIELD_DEFAULT = DateTimeRequiredFieldInterface::DEFAULT_DATE_TIME_REQUIRED;
    protected const VALIDATES          = false;
}

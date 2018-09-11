<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\Boolean;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsEnabledFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsEnabledFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsEnabledFieldTrait
 */
class DefaultsEnabledFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/DefaultsEnabledFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DefaultsEnabledFieldTrait::class;
    protected const TEST_FIELD_PROP    = DefaultsEnabledFieldInterface::PROP_DEFAULTS_ENABLED;
    protected const TEST_FIELD_DEFAULT = DefaultsEnabledFieldInterface::DEFAULT_DEFAULTS_ENABLED;

}

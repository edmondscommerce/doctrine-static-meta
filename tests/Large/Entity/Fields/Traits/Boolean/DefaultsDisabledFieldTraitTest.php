<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\Boolean;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsDisabledFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsDisabledFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsDisabledFieldTrait
 */
class DefaultsDisabledFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/DefaultsDisabledFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DefaultsDisabledFieldTrait::class;
    protected const TEST_FIELD_PROP    = DefaultsDisabledFieldInterface::PROP_DEFAULTS_DISABLED;
    protected const TEST_FIELD_DEFAULT = DefaultsDisabledFieldInterface::DEFAULT_DEFAULTS_DISABLED;
    protected const VALIDATES = false;
}

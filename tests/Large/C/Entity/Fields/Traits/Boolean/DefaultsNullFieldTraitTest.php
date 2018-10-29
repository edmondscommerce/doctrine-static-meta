<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\Boolean;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsNullFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsNullFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean\DefaultsNullFieldTrait
 */
class DefaultsNullFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/DefaultsNullFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DefaultsNullFieldTrait::class;
    protected const TEST_FIELD_PROP    = DefaultsNullFieldInterface::PROP_DEFAULTS_NULL;
    protected const TEST_FIELD_DEFAULT = DefaultsNullFieldInterface::DEFAULT_DEFAULTS_NULL;
    protected const VALIDATES          = false;
}

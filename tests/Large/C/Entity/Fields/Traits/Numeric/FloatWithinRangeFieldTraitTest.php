<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\Numeric;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\FloatWithinRangeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric\FloatWithinRangeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric\FloatWithinRangeFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Numeric\FloatWithinRangeFakerData
 */
class FloatWithinRangeFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/FloatWithinRangeFieldTraitTest/';
    protected const TEST_FIELD_FQN     = FloatWithinRangeFieldTrait::class;
    protected const TEST_FIELD_PROP    = FloatWithinRangeFieldInterface::PROP_FLOAT_WITHIN_RANGE;
    protected const TEST_FIELD_DEFAULT = FloatWithinRangeFieldInterface::DEFAULT_FLOAT_WITHIN_RANGE;
    protected const HAS_SETTER         = true;
    protected const VALIDATES          = true;
    protected const INVALID_VALUES     = [-10, -0.50, 100.12, 'test'];
    protected const VALID_VALUES       = [0.0, 10.54, 55.123, 100.0];
}

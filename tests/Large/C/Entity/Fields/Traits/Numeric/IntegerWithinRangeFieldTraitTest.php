<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\Numeric;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IntegerWithinRangeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric\IntegerWithinRangeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric\IntegerWithinRangeFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Numeric\IntegerWithinRangeFakerData
 */
class IntegerWithinRangeFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/IntegerWithinRangeFieldTraitTest/';
    protected const TEST_FIELD_FQN     = IntegerWithinRangeFieldTrait::class;
    protected const TEST_FIELD_PROP    = IntegerWithinRangeFieldInterface::PROP_INTEGER_WITHIN_RANGE;
    protected const TEST_FIELD_DEFAULT = IntegerWithinRangeFieldInterface::DEFAULT_INTEGER_WITHIN_RANGE;
    protected const HAS_SETTER         = true;
    protected const VALIDATES          = true;
    protected const INVALID_VALUES     = [-10, -1, 101, 'test', 9.9];
    protected const VALID_VALUES       = [0, 10, 55, 100];
}

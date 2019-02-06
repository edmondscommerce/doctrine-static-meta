<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\Numeric;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IndexedUniqueIntegerFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric\IndexedUniqueIntegerFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric\IndexedUniqueIntegerFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Numeric\IndexedUniqueIntegerFakerData
 */
class IndexedUniqueIntegerFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/IndexedUniqueIntegerFieldTraitTest/';
    protected const TEST_FIELD_FQN     = IndexedUniqueIntegerFieldTrait::class;
    protected const TEST_FIELD_PROP    = IndexedUniqueIntegerFieldInterface::PROP_INDEXED_UNIQUE_INTEGER;
    protected const TEST_FIELD_DEFAULT = IndexedUniqueIntegerFieldInterface::DEFAULT_INDEXED_UNIQUE_INTEGER;
    protected const HAS_SETTER         = true;
    protected const VALIDATES          = true;
    protected const INVALID_VALUES     = ['test', null];
    protected const VALID_VALUES       = [0, 10, 55, 100];
}

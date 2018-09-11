<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\ShortIndexedRequiredStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\ShortIndexedRequiredStringFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\ShortIndexedRequiredStringFieldTrait
 */
class ShortIndexedRequiredStringFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/ShortIndexedRequiredStringFieldTraitTest/';
    protected const TEST_FIELD_FQN     = ShortIndexedRequiredStringFieldTrait::class;
    protected const TEST_FIELD_PROP    = ShortIndexedRequiredStringFieldInterface::PROP_SHORT_INDEXED_REQUIRED_STRING;
    protected const TEST_FIELD_DEFAULT =
        ShortIndexedRequiredStringFieldInterface::DEFAULT_SHORT_INDEXED_REQUIRED_STRING;
}

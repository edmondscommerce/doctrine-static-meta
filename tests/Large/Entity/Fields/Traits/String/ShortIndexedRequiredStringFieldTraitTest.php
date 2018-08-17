<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\ShortIndexedRequiredStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class ShortIndexedRequiredStringFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH . '/' . self::TEST_TYPE
                                         . '/ShortIndexedRequiredStringFieldTraitTest/';
    protected const TEST_FIELD_FQN     = ShortIndexedRequiredStringFieldTrait::class;
    protected const TEST_FIELD_PROP    = ShortIndexedRequiredStringFieldInterface::PROP_SHORT_INDEXED_REQUIRED_STRING;
    protected const TEST_FIELD_DEFAULT =
        ShortIndexedRequiredStringFieldInterface::DEFAULT_SHORT_INDEXED_REQUIRED_STRING;
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\NullableStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class NullableStringFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/NullableStringFieldTraitTest/';
    protected const TEST_FIELD_FQN     = NullableStringFieldTrait::class;
    protected const TEST_FIELD_PROP    = NullableStringFieldInterface::PROP_NULLABLE_STRING;
    protected const TEST_FIELD_DEFAULT = NullableStringFieldInterface::DEFAULT_NULLABLE_STRING;
}

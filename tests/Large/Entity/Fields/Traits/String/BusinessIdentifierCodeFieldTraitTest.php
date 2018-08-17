<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\BusinessIdentifierCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class BusinessIdentifierCodeFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH . '/' . self::TEST_TYPE
                                         . '/BusinessIdentifierCodeFieldTraitTest/';
    protected const TEST_FIELD_FQN     = BusinessIdentifierCodeFieldTrait::class;
    protected const TEST_FIELD_PROP    = BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE;
    protected const TEST_FIELD_DEFAULT = BusinessIdentifierCodeFieldInterface::DEFAULT_BUSINESS_IDENTIFIER_CODE;
}

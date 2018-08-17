<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class EmailAddressFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/EmailAddressFieldTraitTest/';
    protected const TEST_FIELD_FQN     = EmailAddressFieldTrait::class;
    protected const TEST_FIELD_PROP    = EmailAddressFieldInterface::PROP_EMAIL_ADDRESS;
    protected const TEST_FIELD_DEFAULT = EmailAddressFieldInterface::DEFAULT_EMAIL_ADDRESS;
}

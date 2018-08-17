<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EmailAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

class EmailAddressFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/EmailAddressFieldTraitTest/';
    protected const TEST_FIELD_FQN     = EmailAddressFieldTrait::class;
    protected const TEST_FIELD_PROP    = EmailAddressFieldInterface::PROP_EMAIL_ADDRESS;
    protected const TEST_FIELD_DEFAULT = EmailAddressFieldInterface::DEFAULT_EMAIL_ADDRESS;
}

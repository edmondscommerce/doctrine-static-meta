<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\LocaleIdentifierFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\LocaleIdentifierFieldInterface;

use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

class LocaleIdentifierFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/LocaleIdentifierFieldTraitTest/';
    protected const TEST_FIELD_FQN     = LocaleIdentifierFieldTrait::class;
    protected const TEST_FIELD_PROP    = LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER;
    protected const TEST_FIELD_DEFAULT = LocaleIdentifierFieldInterface::DEFAULT_LOCALE_IDENTIFIER;
}

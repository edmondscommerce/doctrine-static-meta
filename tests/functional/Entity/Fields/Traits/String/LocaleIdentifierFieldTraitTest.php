<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\LocaleIdentifierFieldInterface;

class LocaleIdentifierFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/LocaleIdentifierFieldTraitTest/';
    protected const TEST_FIELD_FQN =   LocaleIdentifierFieldTrait::class;
    protected const TEST_FIELD_PROP =  LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER;
    protected const TEST_FIELD_DEFAULT = LocaleIdentifierFieldInterface::DEFAULT_LOCALE_IDENTIFIER;
}

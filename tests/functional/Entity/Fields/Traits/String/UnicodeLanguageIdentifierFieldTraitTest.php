<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UnicodeLanguageIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class UnicodeLanguageIdentifierFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH . '/' . self::TEST_TYPE
                                         . '/UnicodeLanguageIdentifierFieldTraitTest/';
    protected const TEST_FIELD_FQN     = UnicodeLanguageIdentifierFieldTrait::class;
    protected const TEST_FIELD_PROP    = UnicodeLanguageIdentifierFieldInterface::PROP_UNICODE_LANGUAGE_IDENTIFIER;
    protected const TEST_FIELD_DEFAULT = UnicodeLanguageIdentifierFieldInterface::DEFAULT_UNICODE_LANGUAGE_IDENTIFIER;
}

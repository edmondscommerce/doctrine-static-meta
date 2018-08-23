<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UnicodeLanguageIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UnicodeLanguageIdentifierFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

class UnicodeLanguageIdentifierFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/UnicodeLanguageIdentifierFieldTraitTest/';
    protected const TEST_FIELD_FQN     = UnicodeLanguageIdentifierFieldTrait::class;
    protected const TEST_FIELD_PROP    = UnicodeLanguageIdentifierFieldInterface::PROP_UNICODE_LANGUAGE_IDENTIFIER;
    protected const TEST_FIELD_DEFAULT = UnicodeLanguageIdentifierFieldInterface::DEFAULT_UNICODE_LANGUAGE_IDENTIFIER;

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UnicodeLanguageIdentifierFieldTrait
     */
    public function createEntityWithField(): void
    {
        parent::createEntityWithField();
    }

    /**
     * @test
     * @large
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UnicodeLanguageIdentifierFieldTrait
     */
    public function createDatabaseSchema(): void
    {
        parent::createDatabaseSchema();
    }
}

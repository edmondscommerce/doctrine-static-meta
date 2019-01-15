<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\JsonDataFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\JsonDataFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Validation\Constraints\FieldConstraints\JsonValidatorTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\JsonDataFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\JsonDataFakerData
 */
class JsonFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE .
                                         '/JsonFieldTraitTest/';
    protected const TEST_FIELD_FQN     = JsonDataFieldTrait::class;
    protected const TEST_FIELD_PROP    = JsonDataFieldInterface::PROP_JSON_DATA;
    protected const TEST_FIELD_DEFAULT = JsonDataFieldInterface::DEFAULT_JSON_DATA;
    protected const VALID_VALUES       = JsonValidatorTest::VALID;
    protected const INVALID_VALUES     = JsonValidatorTest::INVALID;
}

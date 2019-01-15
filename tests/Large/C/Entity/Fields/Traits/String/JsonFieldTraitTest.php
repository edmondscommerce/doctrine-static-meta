<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\JsonFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\JsonFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Validation\Constraints\FieldConstraints\JsonValidatorTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\JsonFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\JsonFakerData
 */
class JsonFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE .
                                         '/JsonFieldTraitTest/';
    protected const TEST_FIELD_FQN     = JsonFieldTrait::class;
    protected const TEST_FIELD_PROP    = JsonFieldInterface::PROP_JSON;
    protected const TEST_FIELD_DEFAULT = JsonFieldInterface::DEFAULT_JSON;
    protected const VALID_VALUES       = JsonValidatorTest::VALID;
    protected const INVALID_VALUES     = JsonValidatorTest::INVALID;
}

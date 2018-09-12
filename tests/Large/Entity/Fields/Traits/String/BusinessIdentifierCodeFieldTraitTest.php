<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\BusinessIdentifierCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\BusinessIdentifierCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\BusinessIdentifierCodeFieldTrait
 */
class BusinessIdentifierCodeFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/BusinessIdentifierCodeFieldTraitTest/';
    protected const TEST_FIELD_FQN     = BusinessIdentifierCodeFieldTrait::class;
    protected const TEST_FIELD_PROP    = BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE;
    protected const TEST_FIELD_DEFAULT = BusinessIdentifierCodeFieldInterface::DEFAULT_BUSINESS_IDENTIFIER_CODE;
    protected const VALID_VALUES       = [
        'DEUTDEFF',
        'NEDSZAJJ',
        'DABADKKK',
    ];
    protected const INVALID_VALUES     = [
        'cheese',
        'www.edmondscommerce.co.uk',
    ];
    protected static $built = false;
}

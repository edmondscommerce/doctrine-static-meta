<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\DomainNameFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\DomainNameFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\DomainNameFieldTrait
 */
class DomainNameFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE .
                                         '/DomainNameFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DomainNameFieldTrait::class;
    protected const TEST_FIELD_PROP    = DomainNameFieldInterface::PROP_DOMAIN_NAME;
    protected const TEST_FIELD_DEFAULT = DomainNameFieldInterface::DEFAULT_DOMAIN_NAME;
    protected const VALID_VALUES       = [
        'edmondscommerce.co.uk',
        'www.google.com',
        'github.io',
    ];
    protected const INVALID_VALUES     = [
        'http://www.edmondscommerce.co.uk',
        'cheese',
    ];
}

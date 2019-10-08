<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UrlFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UrlFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UrlFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UrlFakerData
 */
class UrlFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/UrlFieldTraitTest/';
    protected const TEST_FIELD_FQN     = UrlFieldTrait::class;
    protected const TEST_FIELD_PROP    = UrlFieldInterface::PROP_URL;
    protected const TEST_FIELD_DEFAULT = UrlFieldInterface::DEFAULT_URL;
    protected const VALID_VALUES       = [
        'http://www.edmondscommerce.co.uk',
        'https://www.edmondscommerce.co.uk',
        '//www.edmondscommerce.co.uk',
    ];
    protected const INVALID_VALUES     = [
        'www.edmondscommerce.co.uk',
        'cheese',
        '99',
    ];
}

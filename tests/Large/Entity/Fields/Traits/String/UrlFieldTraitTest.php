<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UrlFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UrlFieldInterface;

use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitLargeTest;

class UrlFieldTraitTest extends AbstractFieldTraitLargeTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE .
                                         '/UrlFieldTraitTest/';
    protected const TEST_FIELD_FQN     = UrlFieldTrait::class;
    protected const TEST_FIELD_PROP    = UrlFieldInterface::PROP_URL;
    protected const TEST_FIELD_DEFAULT = UrlFieldInterface::DEFAULT_URL;
}

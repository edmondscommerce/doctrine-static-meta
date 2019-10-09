<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\IsbnFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\IsbnFakerData
 */
class IsbnFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH .
                                         '/' .
                                         self::TEST_TYPE_LARGE .
                                         '/IsbnFieldTraitTest/';
    protected const TEST_FIELD_FQN     = IsbnFieldTrait::class;
    protected const TEST_FIELD_PROP    = IsbnFieldInterface::PROP_ISBN;
    protected const TEST_FIELD_DEFAULT = IsbnFieldInterface::DEFAULT_ISBN;

    protected const VALID_VALUES   = [
        '978-3-16-148410-0',
        '99921-58-10-7',

    ];
    protected const INVALID_VALUES = [
        'not an isbn',
    ];
}

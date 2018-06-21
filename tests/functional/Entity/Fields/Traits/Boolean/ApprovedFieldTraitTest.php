<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\ApprovedFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class ApprovedFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH.'/'
                                         .self::TEST_TYPE.'/ApprovedFieldTraitTest/';
    protected const TEST_FIELD_FQN     = ApprovedFieldTrait::class;
    protected const TEST_FIELD_PROP    = ApprovedFieldInterface::PROP_APPROVED;
    protected const TEST_FIELD_DEFAULT = ApprovedFieldInterface::DEFAULT_APPROVED;
}

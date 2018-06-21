<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\DeactivatedDateFieldInterface;

class DeactivatedDateFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/DeactivatedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   DeactivatedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  DeactivatedDateFieldInterface::PROP_DEACTIVATED_DATE;
}

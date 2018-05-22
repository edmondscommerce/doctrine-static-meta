<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\DeactivatedDateFieldInterface;

class DeactivatedDateFieldTraitTest extends AbstractFieldTraitIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/DeactivatedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   DeactivatedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  DeactivatedDateFieldInterface::PROP_DEACTIVATED_DATE;
}

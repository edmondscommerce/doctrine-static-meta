<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\DeactivatedDateFieldInterface;

class DeactivatedDateFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/DeactivatedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   DeactivatedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  DeactivatedDateFieldInterface::PROP_DEACTIVATED_DATE;
}

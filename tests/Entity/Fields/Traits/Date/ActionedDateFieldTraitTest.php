<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\ActionedDateFieldInterface;

class ActionedDateFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/ActionedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   ActionedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  ActionedDateFieldInterface::PROP_ACTIONED_DATE;
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\ActionedDateFieldInterface;

class ActionedDateFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/ActionedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   ActionedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  ActionedDateFieldInterface::PROP_ACTIONED_DATE;
}

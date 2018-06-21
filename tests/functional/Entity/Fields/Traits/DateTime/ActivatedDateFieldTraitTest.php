<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\ActivatedDateFieldInterface;

class ActivatedDateFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/ActivatedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   ActivatedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  ActivatedDateFieldInterface::PROP_ACTIVATED_DATE;
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\ActivatedDateFieldInterface;

class ActivatedDateFieldTraitTest extends AbstractFieldTraitIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/ActivatedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   ActivatedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  ActivatedDateFieldInterface::PROP_ACTIVATED_DATE;
}

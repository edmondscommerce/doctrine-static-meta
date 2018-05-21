<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\ActivatedDateFieldInterface;

class ActivatedDateFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/ActivatedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   ActivatedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  ActivatedDateFieldInterface::PROP_ACTIVATED_DATE;
}

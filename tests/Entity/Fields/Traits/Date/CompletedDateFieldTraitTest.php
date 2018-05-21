<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\CompletedDateFieldInterface;

class CompletedDateFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/CompletedDateFieldTraitTest/';
    protected const TEST_FIELD_FQN =   CompletedDateFieldTrait::class;
    protected const TEST_FIELD_PROP =  CompletedDateFieldInterface::PROP_COMPLETED_DATE;
}

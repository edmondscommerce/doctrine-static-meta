<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\ApprovedFieldInterface; 

class ApprovedFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/ApprovedFieldTraitTest/';
    protected const TEST_FIELD_FQN =   ApprovedFieldTrait::class;
    protected const TEST_FIELD_PROP =  ApprovedFieldInterface::PROP_APPROVED;
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\LabelFieldInterface; 

class LabelFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/LabelFieldTraitTest/';
    protected const TEST_FIELD_FQN =   LabelFieldTrait::class;
    protected const TEST_FIELD_PROP =  LabelFieldInterface::PROP_LABEL;
}

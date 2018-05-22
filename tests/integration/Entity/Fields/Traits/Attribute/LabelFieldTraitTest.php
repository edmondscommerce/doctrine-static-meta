<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\LabelFieldInterface;

class LabelFieldTraitTest extends AbstractFieldTraitIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/LabelFieldTraitTest/';
    protected const TEST_FIELD_FQN =   LabelFieldTrait::class;
    protected const TEST_FIELD_PROP =  LabelFieldInterface::PROP_LABEL;
}

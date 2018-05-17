<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\NameFieldInterface;

class NameFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/NameFieldTraitTest/';
    protected const TEST_FIELD_FQN =   NameFieldTrait::class;
    protected const TEST_FIELD_PROP =  NameFieldInterface::PROP_NAME;
}

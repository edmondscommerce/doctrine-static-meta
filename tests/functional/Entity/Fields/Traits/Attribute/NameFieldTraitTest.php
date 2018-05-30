<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\NameFieldInterface;

class NameFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/NameFieldTraitTest/';
    protected const TEST_FIELD_FQN =   NameFieldTrait::class;
    protected const TEST_FIELD_PROP =  NameFieldInterface::PROP_NAME;
}

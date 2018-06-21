<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueStringFieldInterface;

class UniqueStringFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/UniqueStringFieldTraitTest/';
    protected const TEST_FIELD_FQN =   UniqueStringFieldTrait::class;
    protected const TEST_FIELD_PROP =  UniqueStringFieldInterface::PROP_UNIQUE_STRING;
}

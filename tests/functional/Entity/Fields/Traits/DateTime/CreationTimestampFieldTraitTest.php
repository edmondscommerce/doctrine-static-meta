<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\DateTime;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime\CreationTimestampFieldInterface;

class CreationTimestampFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/CreationTimestampFieldTraitTest/';
    protected const TEST_FIELD_FQN =   CreationTimestampFieldTrait::class;
    protected const TEST_FIELD_PROP =  CreationTimestampFieldInterface::PROP_CREATION_TIMESTAMP;
}

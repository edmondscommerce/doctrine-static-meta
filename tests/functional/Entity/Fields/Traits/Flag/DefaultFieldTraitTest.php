<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\DefaultFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;

class DefaultFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR           = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/DefaultFieldTraitTest/';
    protected const TEST_FIELD_FQN     = DefaultFieldTrait::class;
    protected const TEST_FIELD_PROP    = DefaultFieldInterface::PROP_DEFAULT;
    protected const TEST_FIELD_DEFAULT = DefaultFieldInterface::DEFAULT_DEFAULT;
}

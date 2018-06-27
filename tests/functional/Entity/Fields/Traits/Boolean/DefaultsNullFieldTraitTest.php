<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsNullFieldInterface;

class DefaultsNullFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/DefaultsNullFieldTraitTest/';
    protected const TEST_FIELD_FQN =   DefaultsNullFieldTrait::class;
    protected const TEST_FIELD_PROP =  DefaultsNullFieldInterface::PROP_DEFAULTS_NULL;
    protected const TEST_FIELD_DEFAULT = DefaultsNullFieldInterface::DEFAULT_DEFAULTS_NULL;
}

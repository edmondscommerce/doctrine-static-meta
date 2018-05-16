<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\DefaultFieldInterface; 

class DefaultFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/DefaultFieldTraitTest/';
    protected const TEST_FIELD_FQN =   DefaultFieldTrait::class;
    protected const TEST_FIELD_PROP =  DefaultFieldInterface::PROP_DEFAULT;
}

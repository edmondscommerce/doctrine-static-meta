<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person\EmailFieldInterface; 

class EmailFieldTraitTest extends AbstractFieldTraitTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/EmailFieldTraitTest/';
    protected const TEST_FIELD_FQN =   EmailFieldTrait::class;
    protected const TEST_FIELD_PROP =  EmailFieldInterface::PROP_EMAIL;
}

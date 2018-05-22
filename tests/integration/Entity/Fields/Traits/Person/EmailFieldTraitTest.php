<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person\EmailFieldInterface;

class EmailFieldTraitTest extends AbstractFieldTraitIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/EmailFieldTraitTest/';
    protected const TEST_FIELD_FQN =   EmailFieldTrait::class;
    protected const TEST_FIELD_PROP =  EmailFieldInterface::PROP_EMAIL;
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;

class CodeHelperTest extends AbstractTest
{

    public const WORK_DIR = AbstractTest::VAR_PATH.'/CodeHelperTest';

    /**
     * @var CodeHelper
     */
    private $helper;

    public function setup()
    {
        parent::setup();
        $this->helper = $this->container->get(CodeHelper::class);
    }

    public function testfixSuppressWarningsTags()
    {
        $generated = '@SuppressWarnings (Something)';
        $expected  = '@SuppressWarnings(Something)';
        $actual    = $this->helper->fixSuppressWarningsTags($generated);
        $this->assertEquals($expected, $actual);
    }

    public function testMakeConstsPublic()
    {
        $generated = '    const THIS="that"';
        $expected  = '    public const THIS="that"';
        $actual    = $this->helper->makeConstsPublic($generated);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @SuppressWarnings(
     */
    public function testBreakImplementsOntoLines()
    {
        // phpcs:disable
        $generated = '
class Address implements DSM\Interfaces\UsesPHPMetaDataInterface, DSM\Fields\Interfaces\IdFieldInterface, HasCustomers, ReciprocatesCustomer
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasCustomersInverseManyToMany;
}
';
        // phpcs:enable
        $expected = '
class Address implements 
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasCustomers,
    ReciprocatesCustomer
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasCustomersInverseManyToMany;
}
';
        $actual   = $this->helper->breakImplementsOntoLines($generated);
        $this->assertEquals($expected, $actual);
    }

    public function testConstArraysOnMultipleLines()
    {
        // phpcs:disable
        $generated = <<<PHP
class Address
{
    const ITEM = [ 'this'=>1, 'that'=>2 ];
    
    public const ITEM2 = [ 'this'=>1, 'that'=>2 ];
    
    const SCALAR_FIELDS_TO_TYPES = ['domain' => MappingHelper::TYPE_STRING, 'added' => MappingHelper::TYPE_DATETIME, 'checkStatus' => MappingHelper::TYPE_STRING, 'checked' => MappingHelper::TYPE_DATETIME];
}
PHP;
        // phpcs:enable
        $expected = <<<PHP
class Address
{
    const ITEM = [
        'this'=>1,
        'that'=>2
    ];
    
    public const ITEM2 = [
        'this'=>1,
        'that'=>2
    ];
    
    const SCALAR_FIELDS_TO_TYPES = [
        'domain' => MappingHelper::TYPE_STRING,
        'added' => MappingHelper::TYPE_DATETIME,
        'checkStatus' => MappingHelper::TYPE_STRING,
        'checked' => MappingHelper::TYPE_DATETIME
    ];
}
PHP;
        $actual   = $this->helper->constArraysOnMultipleLines($generated);
        $this->assertEquals($expected, $actual);
    }

    public function testPhpcsIgnoreUseSection()
    {
        // phpcs:disable
        $generated = <<<PHP
<?php
declare(strict_types=1);

namespace DSM\GeneratedCodeTest\Project\Entities\Order;

use DSM\GeneratedCodeTest\Project\Entity\Relations\Attributes\Address\Interfaces\HasAddressInterface;
use DSM\GeneratedCodeTest\Project\Entity\Relations\Attributes\Address\Traits\HasAddress\HasAddressUnidirectionalOneToOne;
use DSM\GeneratedCodeTest\Project\Entity\Relations\Order\Interfaces\HasOrderInterface;
use DSM\GeneratedCodeTest\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;
use DSM\GeneratedCodeTest\Project\Entity\Relations\Order\Traits\HasOrder\HasOrderManyToOne;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

class Address implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasOrderInterface,
    ReciprocatesOrderInterface,
    HasAddressInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidateTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasOrderManyToOne;
    use HasAddressUnidirectionalOneToOne;
}

PHP;
        // phpcs:enable
        $expected = <<<PHP
<?php
declare(strict_types=1);

namespace DSM\GeneratedCodeTest\Project\Entities\Order;
// phpcs:disable

use DSM\GeneratedCodeTest\Project\Entity\Relations\Attributes\Address\Interfaces\HasAddressInterface;
use DSM\GeneratedCodeTest\Project\Entity\Relations\Attributes\Address\Traits\HasAddress\HasAddressUnidirectionalOneToOne;
use DSM\GeneratedCodeTest\Project\Entity\Relations\Order\Interfaces\HasOrderInterface;
use DSM\GeneratedCodeTest\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;
use DSM\GeneratedCodeTest\Project\Entity\Relations\Order\Traits\HasOrder\HasOrderManyToOne;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

// phpcs:enable
class Address implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasOrderInterface,
    ReciprocatesOrderInterface,
    HasAddressInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidateTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasOrderManyToOne;
    use HasAddressUnidirectionalOneToOne;
}

PHP;
        $actual   = $this->helper->phpcsIgnoreUseSection($generated);
        $this->assertEquals($expected, $actual);
    }
}

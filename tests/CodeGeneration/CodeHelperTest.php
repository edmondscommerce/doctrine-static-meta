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

    public function testBreakImplementsOntoLines()
    {
        $generated = '
class Address implements DSM\Interfaces\UsesPHPMetaDataInterface, DSM\Interfaces\Fields\IdFieldInterface, HasCustomers, ReciprocatesCustomer
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\Fields\IdFieldTrait;
    use HasCustomersInverseManyToMany;
}
';
        $expected  = '
class Address implements 
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\Fields\IdFieldInterface,
    HasCustomers,
    ReciprocatesCustomer
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\Fields\IdFieldTrait;
    use HasCustomersInverseManyToMany;
}
';
        $actual    = $this->helper->breakImplementsOntoLines($generated);
        $this->assertEquals($expected, $actual);
    }
}

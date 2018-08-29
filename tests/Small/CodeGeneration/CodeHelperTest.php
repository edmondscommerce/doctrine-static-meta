<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class CodeHelperTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper
 */
class CodeHelperTest extends TestCase
{

    /**
     * @var CodeHelper
     */
    private $helper;

    public function setup()
    {
        $this->helper = new CodeHelper(new NamespaceHelper());
    }

    /**
     * @test
     * @small
     * @covers ::classy
     */
    public function classy(): void
    {
        $inputToExpected = [
            'AlreadyClassy' => 'AlreadyClassy',
            'snake_casey'   => 'SnakeCasey',
            'lower'         => 'Lower',
        ];
        $actual          = [];
        foreach (array_keys($inputToExpected) as $input) {
            $actual[$input] = $this->helper->classy($input);
        }
        self::assertSame($inputToExpected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::consty
     */
    public function consty(): void
    {
        $inputToExpected = [
            'ALREADY_CONSTY' => 'ALREADY_CONSTY',
            'snake_casey'    => 'SNAKE_CASEY',
            'lower'          => 'LOWER',
            'WasClassy'      => 'WAS_CLASSY',
        ];
        $actual          = [];
        foreach (array_keys($inputToExpected) as $input) {
            $actual[$input] = $this->helper->consty($input);
        }
        self::assertSame($inputToExpected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::propertyish
     */
    public function propertyish(): void
    {
        $inputToExpected = [
            'alreadyPropertyish' => 'alreadyPropertyish',
            'snake_casey'        => 'snakeCasey',
            'lower'              => 'lower',
            'WasClassy'          => 'wasClassy',
        ];
        $actual          = [];
        foreach (array_keys($inputToExpected) as $input) {
            $actual[$input] = $this->helper->propertyIsh($input);
        }
        self::assertSame($inputToExpected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::fixSuppressWarningsTags
     */
    public function fixSuppressWarningsTags(): void
    {
        $generated = '@SuppressWarnings (Something)';
        $expected  = '@SuppressWarnings(Something)';
        $actual    = $this->helper->fixSuppressWarningsTags($generated);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::makeConstsPublic
     */
    public function makeConstsPublic(): void
    {
        $generated = '<?php
declare(strict_types=1);

namespace BuilderTest_itCanUpdateEnumValueOptions_\Entity\Fields\Interfaces\EntityOne;

interface EnumFieldInterface {

	const DEFAULT_ENUM = self::ENUM_OPTION_THIS;

	const ENUM_OPTION_THAT = \'that\';

	const ENUM_OPTION_THIS = \'this\';

	const ENUM_OPTIONS = [self::ENUM_OPTION_THIS,
	self::ENUM_OPTION_THAT];

	const PROP_ENUM = \'enum\';

	public function getEnum();

	public function setEnum(string $enum);
}
';
        $expected  = '<?php
declare(strict_types=1);

namespace BuilderTest_itCanUpdateEnumValueOptions_\Entity\Fields\Interfaces\EntityOne;

interface EnumFieldInterface {

	public const DEFAULT_ENUM = self::ENUM_OPTION_THIS;

	public const ENUM_OPTION_THAT = \'that\';

	public const ENUM_OPTION_THIS = \'this\';

	public const ENUM_OPTIONS = [self::ENUM_OPTION_THIS,
	self::ENUM_OPTION_THAT];

	public const PROP_ENUM = \'enum\';

	public function getEnum();

	public function setEnum(string $enum);
}
';
        $actual    = $this->helper->makeConstsPublic($generated);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::breakImplementsAndExtendsOntoLines
     */
    public function breakImplementsAndExtendsOntoLines(): void
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
        $actual   = $this->helper->breakImplementsAndExtendsOntoLines($generated);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::constArraysOnMultipleLines
     */
    public function constArraysOnMultipleLines(): void
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
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::phpcsIgnoreUseSection
     */
    public function phpcsIgnoreUseSection(): void
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
        $expected  = <<<PHP
<?php
declare(strict_types=1);

namespace DSM\GeneratedCodeTest\Project\Entities\Order;
// phpcs:disable Generic.Files.LineLength.TooLong

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
        // phpcs:enable
        $actual = $this->helper->phpcsIgnoreUseSection($generated);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @small
     * @covers ::getGetterMethodNameForBoolean
     */
    public function itWillReturnAnIsMethodForABooleanField(): void
    {
        $fieldName  = 'testField';
        $methodName = $this->helper->getGetterMethodNameForBoolean($fieldName);
        self::assertSame('isTestField', $methodName);
    }

    /**
     * @test
     * @small
     * @covers ::getGetterMethodNameForBoolean
     */
    public function itWillNotReturnIsTwice(): void
    {
        $fieldName  = 'isReadOnly';
        $methodName = $this->helper->getGetterMethodNameForBoolean($fieldName);
        self::assertSame($fieldName, $methodName);
    }

    /**
     * @test
     * @small
     * @covers ::getGetterMethodNameForBoolean
     */
    public function itWillNotReturnIsHasInTheMethodName(): void
    {
        $fieldName  = 'hasHeaders';
        $methodName = $this->helper->getGetterMethodNameForBoolean($fieldName);
        self::assertSame($fieldName, $methodName);
    }
}

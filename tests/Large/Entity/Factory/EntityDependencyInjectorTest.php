<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataDataValidator;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Symfony\Component\Filesystem\Filesystem;

class EntityDependencyInjectorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityDependencyInjectorTest/';

    private const TEST_ENTITY_FILE = '/src/Entities/Order.php';
    private const TEST_ENTITY_FQN  = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER;

    protected static $buildOnce = true;

    /**
     * @var EntityDependencyInjector
     */
    private $injector;
    /**
     * @var string
     */
    private $entityFqn;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            $this->overrideOrderEntity();
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->entityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->injector  = new EntityDependencyInjector($this->container);
    }

    private function overrideOrderEntity()
    {
        \ts\file_put_contents(
            self::WORK_DIR . self::TEST_ENTITY_FILE,
            /** @lang PHP */
            <<<'PHP'
<?php declare(strict_types=1);

namespace My\Test\Project\Entities;
// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Fields\Traits\BooleanFieldTrait;
use My\Test\Project\Entity\Fields\Traits\DatetimeFieldTrait;
use My\Test\Project\Entity\Fields\Traits\DecimalFieldTrait;
use My\Test\Project\Entity\Fields\Traits\FloatFieldTrait;
use My\Test\Project\Entity\Fields\Traits\IntegerFieldTrait;
use My\Test\Project\Entity\Fields\Traits\JsonFieldTrait;
use My\Test\Project\Entity\Fields\Traits\StringFieldTrait;
use My\Test\Project\Entity\Fields\Traits\TextFieldTrait;
use My\Test\Project\Entity\Interfaces\OrderInterface;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddresses\HasOrderAddressesOneToMany;
use My\Test\Project\Entity\Relations\Person\Traits\HasPerson\HasPersonManyToOne;
use My\Test\Project\Entity\Repositories\OrderRepository;use Symfony\Component\Filesystem\Filesystem;

// phpcs:enable
class Order implements 
    OrderInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidatedEntityTrait;
	use DSM\Traits\ImplementNotifyChangeTrackingPolicy;
	use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;
    use DSM\Traits\AlwaysValidTrait;
	use StringFieldTrait;
	use DatetimeFieldTrait;
	use FloatFieldTrait;
	use DecimalFieldTrait;
	use IntegerFieldTrait;
	use TextFieldTrait;
	use BooleanFieldTrait;
	use JsonFieldTrait;
	use HasPersonManyToOne;
	use HasOrderAddressesOneToMany;
	
	private $filesystem;
	private static $namespaceHelper;

	/**
	 * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @param ClassMetadataBuilder $builder
	 */
	private static function setCustomRepositoryClass(ClassMetadataBuilder $builder) {
		$builder->setCustomRepositoryClass(OrderRepository::class);
	}

	public function __construct() {
		$this->runInitMethods();
	}

	public function injectFilesystem(Filesystem $filesystem){
	    $this->filesystem=$filesystem;
	}
	
	public static function injectNamespaceHelper(NamespaceHelper $namespaceHelper){
	    self::$namespaceHelper=$namespaceHelper;
	}
	
	public  function getFilesystem():Filesystem{
        return $this->filesystem;
    }
    
    public static function getNamespaceHelper(): NamespaceHelper{
        return self::$namespaceHelper;
    }
    
    public function getValidator():DSM\Validation\EntityDataDataValidator{
	    return $this->validator;
    }
}
PHP
        );
    }

    /**
     * @test
     * @large
     */
    public function itCanInjectDependencies()
    {
        $entity = $this->createOrderEntity();
        $this->injector->injectEntityDependencies($entity);
        self::assertInstanceOf(Filesystem::class, $entity->getFilesystem());
        self::assertInstanceOf(NamespaceHelper::class, $entity::getNamespaceHelper());
    }

    private function createOrderEntity()
    {
        $entityFqn = $this->entityFqn;

        return new $entityFqn();
    }

    /**
     * @test
     * @large
     */
    public function itThrowsAnExceptionIfAnInjectMethodDoesNotHaveOnlyOneParam()
    {
        \ts\file_put_contents(
            $this->copiedWorkDir . self::TEST_ENTITY_FILE,
            \str_replace(
                'public static function injectNamespaceHelper(NamespaceHelper $namespaceHelper){',
                'public static function injectNamespaceHelper(NamespaceHelper $namespaceHelper, bool $thing){',
                \ts\file_get_contents($this->copiedWorkDir . self::TEST_ENTITY_FILE)
            )
        );
        $entity = $this->createOrderEntity();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Invalid method signature for injectNamespaceHelper, ' .
            'should only take one argument which is the dependency to be injected'
        );
        $this->injector->injectEntityDependencies($entity);
    }

    /**
     * @test
     * @large
     */
    public function itThrowsAnExceptionIfAnInjectMethodHasNoTypeHint()
    {
        \ts\file_put_contents(
            $this->copiedWorkDir . self::TEST_ENTITY_FILE,
            \str_replace(
                'public static function injectNamespaceHelper(NamespaceHelper $namespaceHelper){',
                'public static function injectNamespaceHelper($namespaceHelper){',
                \ts\file_get_contents($this->copiedWorkDir . self::TEST_ENTITY_FILE)
            )
        );
        $entity = $this->createOrderEntity();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Invalid method signature for injectNamespaceHelper, the object being set must be type hinted'
        );
        $this->injector->injectEntityDependencies($entity);
    }
}

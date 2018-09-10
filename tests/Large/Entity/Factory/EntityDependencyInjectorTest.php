<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Symfony\Component\Filesystem\Filesystem;

class EntityDependencyInjectorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/EntityDependencyInjectorTest/';

    private const TEST_ENTITY_FILE = self::WORK_DIR . '/src/Entities/Order.php';
    private const TEST_ENTITY_FQN  = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Order';

    protected static $buildOnce = true;

    /**
     * @var EntityDependencyInjector
     */
    private $injector;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built    = true;
            $this->injector = new EntityDependencyInjector();
            $this->injector->addEntityDependency(self::TEST_ENTITY_FQN, $this->container->get(Filesystem::class));
            $this->injector->addEntityDependency(self::TEST_ENTITY_FQN, $this->container->get(NamespaceHelper::class));
        }
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
        $entityFqn = self::TEST_ENTITY_FQN;

        return new $entityFqn($this->container->get(EntityValidatorFactory::class));
    }

    public function itCanInjectStaticDependencies()
    {
    }

    public function itThrowsAnExceptionIfAnInjectMethodDoesNotHaveOnlyOneParam()
    {
    }

    public function itThrowsAnExceptionIfAnInjectMethodHasNoTypeHint()
    {
    }

    private function overrideOrderEntity()
    {
        file_put_contents(
            self::TEST_ENTITY_FILE,
            /** @lang PHP */
            <<<'PHP'
<?php declare(strict_types=1);

namespace Test\Code\Generator\Entities;
// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use Symfony\Component\Filesystem\Filesystem;use Test\Code\Generator\Entity\Fields\Traits\BooleanFieldTrait;
use Test\Code\Generator\Entity\Fields\Traits\DatetimeFieldTrait;
use Test\Code\Generator\Entity\Fields\Traits\DecimalFieldTrait;
use Test\Code\Generator\Entity\Fields\Traits\FloatFieldTrait;
use Test\Code\Generator\Entity\Fields\Traits\IntegerFieldTrait;
use Test\Code\Generator\Entity\Fields\Traits\JsonFieldTrait;
use Test\Code\Generator\Entity\Fields\Traits\StringFieldTrait;
use Test\Code\Generator\Entity\Fields\Traits\TextFieldTrait;
use Test\Code\Generator\Entity\Interfaces\OrderInterface;
use Test\Code\Generator\Entity\Relations\Order\Address\Traits\HasOrderAddresses\HasOrderAddressesOneToMany;
use Test\Code\Generator\Entity\Relations\Person\Traits\HasPerson\HasPersonManyToOne;
use Test\Code\Generator\Entity\Repositories\OrderRepository;

// phpcs:enable
class Order implements
    OrderInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidatedEntityTrait;
	use DSM\Traits\ImplementNotifyChangeTrackingPolicy;
	use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;
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
	/**
    * @var Filesystem 
    */
	private $filesystem;
	
	/**
    * @var NamespaceHelper
    */
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

	public function __construct(DSM\Validation\EntityValidatorFactory $entityValidatorFactory) {
		$this->runInitMethods();
		$this->injectValidator($entityValidatorFactory->getEntityValidator());
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
}
PHP
        );
    }
}

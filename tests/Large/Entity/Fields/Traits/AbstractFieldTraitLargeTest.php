<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\FakerDataProviderInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityTestInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use Faker\Generator;

/**
 * Extend this test with your Field Trait test to get basic test coverage.
 *
 * You should extend your field trait test to test your validation
 *
 * Class AbstractFieldTraitLargeTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractFieldTraitLargeTest extends AbstractLargeTest
{
    protected const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                           . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                                           . '\\';

    protected const TEST_FIELD_FQN = 'Override Me';

    /**
     * The expected default value for the field. Most fields are marked as nullable so the default is null.
     * Should be overriden in the actual field test for any fields that are not nullable
     */
    protected const TEST_FIELD_DEFAULT = null;

    protected const TEST_FIELD_PROP = 'Override Me';

    /**
     * set to false for read only fields (with no setter)
     */
    protected const HAS_SETTER = true;
    /**
     * @var Generator
     */
    protected static $fakerGenerator;
    protected $entitySuffix;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setUpBeforeClass()
    {
        /* The :void return type declaration that should be here would cause a BC issue */
        self::$fakerGenerator = \Faker\Factory::create();
    }

    public function setup()
    {
        parent::setup();
        $this->entitySuffix = substr(static::class, strrpos(static::class, '\\') + 1);
        $this->generateCode();
    }

    protected function generateCode()
    {
        $this->getEntityGenerator()
             ->generateEntity(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
        $this->getFieldSetter()
             ->setEntityHasField(
                 static::TEST_ENTITY_FQN_BASE . $this->entitySuffix,
                 static::TEST_FIELD_FQN
             );
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testCreateEntityWithField(): void
    {
        $this->setupCopiedWorkDir();
        $entityFqn = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
        $entity    = new $entityFqn($this->container->get(EntityValidatorFactory::class));
        $getter    = $this->getGetter($entity);
        self::assertTrue(\method_exists($entity, $getter));
        $value = $entity->$getter();
        self::assertSame(
            static::TEST_FIELD_DEFAULT,
            $value,
            'The getter on a newly created entity returns ' . var_export($value, true)
            . ' whereas the configured default value is ' . var_export(static::TEST_FIELD_DEFAULT, true)
        );
        if (false === static::HAS_SETTER) {
            return;
        }
        $setValue = $this->setFakerValueForProperty($entity);
        self::assertSame($setValue, $entity->$getter());
    }

    /**
     * @param EntityInterface $entity
     *
     * @return string
     * @throws \Exception
     */
    protected function getGetter(EntityInterface $entity): string
    {
        foreach (['get', 'is', 'has'] as $prefix) {
            $method = $prefix . static::TEST_FIELD_PROP;
            if (\method_exists($entity, $method)) {
                return $method;
            }
        }
        throw new \RuntimeException('Failed finding a getter in ' . __METHOD__);
    }

    /**
     * @param EntityInterface $entity
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function setFakerValueForProperty(EntityInterface $entity)
    {
        $setter        = 'set' . static::TEST_FIELD_PROP;
        $fakerProvider = $this->getFakerDataProvider();
        if ($fakerProvider instanceof FakerDataProviderInterface) {
            $setValue = $fakerProvider();
            $entity->$setter($setValue);

            return $setValue;
        }
        $reflection       = new  \ts\Reflection\ReflectionClass(\get_class($entity));
        $setterReflection = $reflection->getMethod($setter);
        $setParamType     = current($setterReflection->getParameters())->getType()->getName();
        switch ($setParamType) {
            case 'string':
                $setValue = self::$fakerGenerator->text();
                break;
            case 'int':
                $setValue = self::$fakerGenerator->numberBetween(0, PHP_INT_MAX);
                break;
            case 'float':
                $setValue = self::$fakerGenerator->randomFloat(12, 0, 10000);
                break;
            case 'bool':
                $setValue = self::$fakerGenerator->boolean;
                break;
            case 'DateTime':
                $setValue = self::$fakerGenerator->dateTime;
                break;
            case 'DateTimeImmutable':
                $setValue = new \DateTimeImmutable(
                    self::$fakerGenerator->dateTime->format('Y-m-d')
                );
                break;
            default:
                throw new \RuntimeException('Failed getting a data provider for the property type ' . $setParamType);
        }
        $entity->$setter($setValue);

        return $setValue;
    }

    protected function getFakerDataProvider(): ?FakerDataProviderInterface
    {
        if (isset(EntityTestInterface::FAKER_DATA_PROVIDERS[static::TEST_FIELD_PROP])) {
            $provider = EntityTestInterface::FAKER_DATA_PROVIDERS[static::TEST_FIELD_PROP];

            return new $provider(self::$fakerGenerator);
        }

        return null;
    }

    public function testCreateDatabaseSchema()
    {
        $this->setupCopiedWorkDirAndCreateDatabase();
        $entityManager = $this->getEntityManager();
        $entityFqn     = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
        $entity        = new $entityFqn($this->container->get(EntityValidatorFactory::class));
        $setValue      = null;
        if (false !== static::HAS_SETTER) {
            $setValue = $this->setFakerValueForProperty($entity);
        }
        $saver = $this->container->get(EntitySaver::class);
        $saver->save($entity);
        $repository  = $entityManager->getRepository($entityFqn);
        $entities    = $repository->findAll();
        $savedEntity = current($entities);
        $getter      = $this->getGetter($entity);
        $gotValue    = $savedEntity->$getter();
        if (false !== static::HAS_SETTER) {
            self::assertEquals($setValue, $gotValue);

            return;
        }
        self::assertNotNull($gotValue);
    }
}

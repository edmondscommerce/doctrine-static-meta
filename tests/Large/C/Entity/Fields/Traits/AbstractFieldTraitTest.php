<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\FakerDataProviderInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityTestInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use Faker\Generator;

/**
 * Extend this test with your Field Trait test to get basic test coverage.
 *
 * You should extend your field trait test to test your validation
 *
 * Class AbstractFieldTraitTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @large
 */
abstract class AbstractFieldTraitTest extends AbstractLargeTest
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
     * set to false for fields that do not have a validator configuration
     */
    protected const VALIDATES = true;

    /**
     * Override this with an array of valid values to set
     */
    protected const VALID_VALUES = [];

    /**
     * Override this with an array of invalid values to set.
     */
    protected const INVALID_VALUES = [];

    /**
     * @var Generator
     */
    protected static $fakerGenerator;
    protected static $buildOnce = true;
    protected        $entitySuffix;

    public function setup()
    {
        parent::setUp();
        $this->entitySuffix = substr(static::class, strrpos(static::class, '\\') + 1);
        if (false === static::$built) {
            $this->generateCode();
            static::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->recreateDtos();
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$fakerGenerator = \Faker\Factory::create();
    }

    /**
     * @throws \ReflectionException
     * @large
     * @test
     */
    public function createEntityWithField(): void
    {
        $entity = $this->getEntity();
        $getter = $this->getGetter($entity);
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

    protected function getEntity()
    {
        return $this->createEntity($this->getEntityFqn());
    }

    protected function getEntityFqn(): string
    {
        return $this->getCopiedFqn(self::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
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
            $this->updateWithDto($setter, $entity, $setValue);

            return $setValue;
        }

        $reflection       = new  \ts\Reflection\ReflectionClass(\get_class($entity));
        $setterReflection = $reflection->getMethod($setter);

        $setParamType = current($setterReflection->getParameters())->getType()->getName();
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
        $this->updateWithDto($setter, $entity, $setValue);

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

    private function updateWithDto(string $setterName, EntityInterface $entity, $setValue): void
    {
        $dto = $this->getEntityDtoFactory()->createDtoFromEntity($entity);
        $dto->$setterName($setValue);
        $entity->update($dto);
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function createDatabaseSchema()
    {
        $this->createDatabase();
        $entity   = $this->getEntity();
        $setValue = null;
        if (false !== static::HAS_SETTER) {
            $setValue = $this->setFakerValueForProperty($entity);
        }
        $saver = $this->container->get(EntitySaver::class);
        $saver->save($entity);
        $reloadedEntity = $this->clearEntityManagerAndReloadEntity();
        $getter         = $this->getGetter($entity);
        $gotValue       = $reloadedEntity->$getter();
        if (false !== static::HAS_SETTER) {
            self::assertEquals($setValue, $gotValue);

            return;
        }
        self::assertNotNull($gotValue);
    }

    protected function clearEntityManagerAndReloadEntity(): EntityInterface
    {
        $this->getEntityManager()->clear();
        $repository = $this->getRepositoryFactory()->getRepository($this->getEntityFqn());
        $entities   = $repository->findAll();

        return current($entities);
    }

    /**
     * @test
     * @large
     */
    public function validValuesAreAccepted(): void
    {
        if (false === static::VALIDATES) {
            self::markTestSkipped('This field does has no validation');
        }
        if (false === static::HAS_SETTER) {
            self::markTestSkipped('No setter for this field');
        }
        if ([] === static::VALID_VALUES) {
            self::fail('You need to assign some valid values to ' . static::class . '::VALID_VALUES');
        }
        $entity = $this->getEntity();
        $setter = 'set' . static::TEST_FIELD_PROP;
        $getter = $this->getGetter($entity);
        foreach (static::VALID_VALUES as $value) {
            $this->updateWithDto($setter, $entity, $value);
            self::assertSame($value, $entity->$getter());
        }
    }

    /**
     * @test
     * @large
     * @dataProvider invalidValuesProvider
     */
    public function invalidValuesAreNotAccepted($invalidValue): void
    {
        if (false === static::VALIDATES) {
            self::markTestSkipped('This field does has no validation');
        }
        if (false === static::HAS_SETTER) {
            self::markTestSkipped('No setter for this field');
        }
        $entity = $this->getEntity();
        $setter = 'set' . static::TEST_FIELD_PROP;
        $this->expectException(ValidationException::class);
        try {
            $this->updateWithDto($setter, $entity, $invalidValue);
        } catch (\TypeError $e) {
            self::markTestSkipped(
                'You have set an INVALID_VALUE item of ' .
                $invalidValue .
                ' which has caused a TypeError as the setter does not accept this type of value.'
            );
        }
    }

    /**
     * Yield the invalid data, keyed by a namespace safe version of the value
     *
     * @return \Generator
     */
    public function invalidValuesProvider(): \Generator
    {
        if (false === static::VALIDATES) {
            self::markTestSkipped('This field does not validate');
        }
        if ([] === static::INVALID_VALUES) {
            self::fail('You need to assign some invalid values to ' . static::class . '::INVALID_VALUES');
        }
        foreach (static::INVALID_VALUES as $invalidValue) {
            yield $invalidValue => [$invalidValue];
        }
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\FakerDataProviderInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Faker\Generator;

abstract class AbstractFieldTraitTest extends AbstractTest
{
    protected const TEST_ENTITY_FQN_BASE = AbstractTest::TEST_PROJECT_ROOT_NAMESPACE
                                           .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME
                                           .'\\';

    protected const TEST_FIELD_FQN = 'Override Me';

    /**
     * The expected default value for the field. Most fields are marked as nullable so the default is null.
     * Should be overriden in the actual field test for any fields that are not nullable
     */
    protected const TEST_FIELD_DEFAULT = null;

    protected const TEST_FIELD_PROP = 'Override Me';

    protected $entitySuffix;

    /**
     * @var Generator
     */
    protected static $fakerGenerator;

    public static function setUpBeforeClass(
    )/* The :void return type declaration that should be here would cause a BC issue */
    {
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
             ->generateEntity(static::TEST_ENTITY_FQN_BASE.$this->entitySuffix);
        $this->getFieldGenerator()
             ->setEntityHasField(
                 static::TEST_ENTITY_FQN_BASE.$this->entitySuffix,
                 static::TEST_FIELD_FQN
             );
        $this->setupCopiedWorkDir();
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
            $method = $prefix.static::TEST_FIELD_PROP;
            if (\method_exists($entity, $method)) {
                return $method;
            }
        }
        throw new \RuntimeException('Failed finding a getter in '.__METHOD__);
    }

    protected function findFakerProvider(): ?FakerDataProviderInterface
    {
        $fakerProviderFqnBase = 'EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData';
        $traitBase            = 'EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits';
        $fakerFqn             = \str_replace(
            [
                $traitBase,
                'FieldTrait',
            ],
            [
                $fakerProviderFqnBase,
                'FakerData',
            ],
            static::TEST_FIELD_FQN
        );
        if (class_exists($fakerFqn)) {
            return new $fakerFqn(self::$fakerGenerator);
        }

        return null;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return mixed
     * @throws \ReflectionException
     */
    protected function setFakerValueForProperty(EntityInterface $entity)
    {
        $setter        = 'set'.static::TEST_FIELD_PROP;
        $fakerProvider = $this->findFakerProvider();
        if ($fakerProvider instanceof FakerDataProviderInterface) {
            $setValue = $fakerProvider();
            $entity->$setter($setValue);

            return $setValue;
        }
        $reflection       = new \ReflectionClass($entity);
        $setterReflection = $reflection->getMethod($setter);
        $setParamType     = current($setterReflection->getParameters())->getType()->getName();
        switch ($setParamType) {
            case 'string':
                $setValue = self::$fakerGenerator->text();
                break;
            case 'int':
            case 'float':
                $setValue = self::$fakerGenerator->numberBetween(0, 100);
                break;
            case 'bool':
                $setValue = self::$fakerGenerator->boolean;
                break;
            case 'DateTime':
                $setValue = self::$fakerGenerator->dateTime;
                break;
            default:
                throw new \RuntimeException('Failed getting a data provider for the property type '.$setParamType);
        }
        $entity->$setter($setValue);

        return $setValue;
    }


    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testCreateEntityWithField(): void
    {
        $entityFqn = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE.$this->entitySuffix);
        $entity    = new $entityFqn();
        $getter    = $this->getGetter($entity);
        $this->assertTrue(\method_exists($entity, $getter));
        $value = $entity->$getter();
        $this->assertSame(static::TEST_FIELD_DEFAULT, $value);
        $setValue = $this->setFakerValueForProperty($entity);
        $this->assertSame($setValue, $entity->$getter());
    }
}

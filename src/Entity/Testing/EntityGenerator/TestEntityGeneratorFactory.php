<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use ts\Reflection\ReflectionClass;

class TestEntityGeneratorFactory
{
    /**
     * @var EntitySaverFactory
     */
    protected $entitySaverFactory;
    /**
     * @var EntityValidatorFactory
     */
    protected $entityValidatorFactory;
    /**
     * @var array
     */
    protected $fakerDataProviderClasses;
    /**
     * @var float|null
     */
    protected $seed;
    /**
     * @var EntityFactory|null
     */
    protected $entityFactory;

    public function __construct(
        EntitySaverFactory $entitySaverFactory,
        EntityValidatorFactory $entityValidatorFactory,
        array $fakerDataProviderClasses = [],
        ?float $seed = null,
        ?EntityFactory $entityFactory = null
    ) {

        $this->entitySaverFactory       = $entitySaverFactory;
        $this->entityValidatorFactory   = $entityValidatorFactory;
        $this->fakerDataProviderClasses = $fakerDataProviderClasses;
        $this->seed                     = $seed;
        $this->entityFactory            = $entityFactory;
    }

    public function createForEntityFqn(
        string $entityFqn
    ): TestEntityGenerator {
        return $this->createForEntityReflection(new ReflectionClass($entityFqn));
    }

    public function createForEntityReflection(ReflectionClass $testedEntityReflectionClass): TestEntityGenerator
    {
        return new TestEntityGenerator(
            $this->fakerDataProviderClasses,
            $testedEntityReflectionClass,
            $this->entitySaverFactory,
            $this->entityFactory,
            $this->seed
        );
    }

    /**
     * @param float $seed
     *
     * @return TestEntityGeneratorFactory
     */
    public function setSeed(float $seed): TestEntityGeneratorFactory
    {
        $this->seed = $seed;

        return $this;
    }

    /**
     * @param array $fakerDataProviderClasses
     *
     * @return TestEntityGeneratorFactory
     */
    public function setFakerDataProviderClasses(array $fakerDataProviderClasses): TestEntityGeneratorFactory
    {
        $this->fakerDataProviderClasses = $fakerDataProviderClasses;

        return $this;
    }
}

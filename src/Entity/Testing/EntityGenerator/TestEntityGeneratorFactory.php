<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator;

use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\ValidatorFactory;
use ts\Reflection\ReflectionClass;

class TestEntityGeneratorFactory
{
    /**
     * @var EntitySaverFactory
     */
    protected $entitySaverFactory;
    /**
     * @var ValidatorFactory
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
     * @var EntityFactoryInterface|null
     */
    protected $entityFactory;
    /**
     * @var DtoFactory
     */
    private $dtoFactory;

    public function __construct(
        EntitySaverFactory $entitySaverFactory,
        EntityFactoryInterface $entityFactory,
        DtoFactory $dtoFactory,
        array $fakerDataProviderClasses = [],
        ?float $seed = null
    ) {
        $this->entitySaverFactory       = $entitySaverFactory;
        $this->entityFactory            = $entityFactory;
        $this->dtoFactory               = $dtoFactory;
        $this->fakerDataProviderClasses = $fakerDataProviderClasses;
        $this->seed                     = $seed;
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
            $this->dtoFactory,
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

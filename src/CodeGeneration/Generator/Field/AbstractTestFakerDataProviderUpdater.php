<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\CodeGenClassTypeFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessorInterface;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpConstant;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Constant;
use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * Class AbstractTestFakerDataProviderUpdater
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AbstractTestFakerDataProviderUpdater
{
    public const FAKER_DATA_PROVIDERS_CONST_NAME = 'FAKER_DATA_PROVIDERS';

    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;
    /**
     * @var CodeHelper
     */
    private $codeHelper;
    /**
     * @var string
     */
    private $fieldFqn;
    /**
     * @var string
     */
    private $entityFqn;
    /**
     * @var string
     */
    private $fakerFqn;
    /**
     * @var string
     */
    private $interfaceFqn;
    /**
     * @var string
     */
    private $abstractTestPath;
    /**
     * @var string
     */
    private $newPropertyConst;
    /**
     * @var CodeGenClassTypeFactory
     */
    private $codeGenClassTypeFactory;

    public function __construct(
        NamespaceHelper $namespaceHelper,
        CodeHelper $codeHelper,
        CodeGenClassTypeFactory $codeGenClassTypeFactory
    ) {
        $this->namespaceHelper         = $namespaceHelper;
        $this->codeHelper              = $codeHelper;
        $this->codeGenClassTypeFactory = $codeGenClassTypeFactory;
    }

    public function updateFakerProviderArray(string $projectNamespaceRoot, string $fieldFqn, string $entityFqn): void
    {
        $this->fieldFqn         = $fieldFqn;
        $fieldFqnBase           = \str_replace('FieldTrait', '', $this->fieldFqn);
        $this->entityFqn        = $entityFqn;
        $this->fakerFqn         = $this->namespaceHelper->tidy(
                \str_replace('\\Traits\\', '\\FakerData\\', $fieldFqnBase)
            ) . 'FakerData';
        $this->interfaceFqn     = $this->namespaceHelper->tidy(
            \str_replace(
                '\\Traits\\',
                '\\Interfaces\\',
                $fieldFqnBase
            ) . 'FieldInterface'
        );
        $abstractTestFqn        = $projectNamespaceRoot . '\\Entities\\AbstractEntityTest';
        $abstractTestReflection = ReflectionClass::createFromName($abstractTestFqn);
        $abstractTestClassType  = $this->codeGenClassTypeFactory->createFromBetterReflection($abstractTestReflection);
        $this->newPropertyConst = 'PROP_' . $this->codeHelper->consty($this->namespaceHelper->basename($fieldFqnBase));
        $this->update($abstractTestClassType);
        $this->codeHelper->generate(
            $abstractTestClassType,
            $abstractTestReflection->getFileName(),
            new class implements PostProcessorInterface
            {
                public function __invoke(string $generated): string
                {
                    return \str_replace('// phpcs:enable', '', $generated);
                }
            }
        );
    }

    private function update(ClassType $abstractTestClassType): void
    {
        $constant = $this->findConstant($abstractTestClassType);
        if ($constant instanceof Constant) {
            $this->updateExisting($abstractTestClassType, $constant);

            return;
        }
        $this->createNew($abstractTestClassType);
    }

    private function findConstant(ClassType $test): ?Constant
    {
        $constants = $test->getConstants();
        foreach ($constants as $constant) {
            if (self::FAKER_DATA_PROVIDERS_CONST_NAME === $constant->getName()) {
                return $constant;
            }
        }

        return null;
    }

    private function updateExisting(ClassType $abstractTestClassType, Constant $constant): void
    {

        $abstractTestClassType->removeConstant(self::FAKER_DATA_PROVIDERS_CONST_NAME);
        $value = $constant->getValue();
        $value = \str_replace(
            ']',
            ",{$this->getLine()}]",
            $value
        );
        $abstractTestClassType->addConstant(self::FAKER_DATA_PROVIDERS_CONST_NAME, $value);
    }

    /**
     * Get the line that we are going to add to the array
     *
     * @return string
     */
    private function getLine(): string
    {
        return "\n'$this->entityFqn-'.\\$this->interfaceFqn::$this->newPropertyConst => \\$this->fakerFqn::class\n";
    }

    private function createNew(ClassType $abstractTestClassType): void
    {
        $abstractTestClassType->addConstant(self::FAKER_DATA_PROVIDERS_CONST_NAME, $this->getLine());
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpConstant;

/**
 * Class AbstractTestFakerDataProviderUpdater
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class AbstractTestFakerDataProviderUpdater
{
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
    private $projectRootPath;
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

    public function __construct(NamespaceHelper $namespaceHelper, CodeHelper $codeHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
        $this->codeHelper      = $codeHelper;
    }

    public function updateFakerProviderArray(string $projectRootPath, string $fieldFqn, string $entityFqn)
    {
        $this->projectRootPath  = $projectRootPath;
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
        $this->abstractTestPath = $this->projectRootPath . '/tests/Entities/AbstractEntityTest.php';
        $test                   = PhpClass::fromFile($this->abstractTestPath);
        $this->newPropertyConst = 'PROP_' . $this->codeHelper->consty($this->namespaceHelper->basename($fieldFqnBase));
        try {
            $constant = $this->updateExisting($test);
        } catch (\InvalidArgumentException $e) {
            $constant = $this->createNew();
        }
        $test->setConstant($constant);
        $this->codeHelper->generate($test, $this->abstractTestPath);
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

    private function updateExisting(PhpClass $test): PhpConstant
    {
        $constant = $test->getConstant('FAKER_DATA_PROVIDERS');
        $test->removeConstant($constant);
        $expression = $constant->getExpression();
        $expression = \str_replace(
            ']',
            ",{$this->getLine()}]",
            $expression
        );
        $constant->setExpression($expression);

        return $constant;
    }

    private function createNew(): PhpConstant
    {
        return new PhpConstant(
            'FAKER_DATA_PROVIDERS',
            "[\n{$this->getLine()}]",
            true
        );
    }
}

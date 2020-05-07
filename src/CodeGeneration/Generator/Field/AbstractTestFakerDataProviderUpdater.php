<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessorInterface;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpConstant;
use InvalidArgumentException;

use function str_replace;
use function substr;

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
    private NamespaceHelper $namespaceHelper;
    /**
     * @var CodeHelper
     */
    private CodeHelper $codeHelper;
    /**
     * @var string
     */
    private string $entityFqn;

    /**
     * @var string
     */
    private string $projectRootPath;
    /**
     * @var string
     */
    private string $fakerFqn;
    /**
     * @var string
     */
    private string $interfaceFqn;
    /**
     * @var string
     */
    private string $abstractTestPath;
    /**
     * @var string
     */
    private string $newPropertyConst;

    public function __construct(NamespaceHelper $namespaceHelper, CodeHelper $codeHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
        $this->codeHelper      = $codeHelper;
    }

    public function updateFakerProviderArrayWithFieldFakerData(
        string $projectRootPath,
        string $fieldFqn,
        string $entityFqn
    ): void {
        $this->projectRootPath  = $projectRootPath;
        $fieldFqnBase           = str_replace('FieldTrait', '', $fieldFqn);
        $this->entityFqn        = $entityFqn;
        $this->fakerFqn         = $this->namespaceHelper->tidy(
            str_replace('\\Traits\\', '\\FakerData\\', $fieldFqnBase)
        ) . 'FakerData';
        $this->interfaceFqn     = $this->namespaceHelper->tidy(
            str_replace(
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
        } catch (InvalidArgumentException $e) {
            $constant = $this->createNew();
        }
        $test->setConstant($constant);
        $this->codeHelper->generate(
            $test,
            $this->abstractTestPath,
            new class implements PostProcessorInterface
            {
                public function __invoke(string $generated): string
                {
                    return str_replace('// phpcs:enable', '', $generated);
                }
            }
        );
    }

    private function updateExisting(PhpClass $test): PhpConstant
    {
        $constant = $test->getConstant('FAKER_DATA_PROVIDERS');
        $test->removeConstant($constant);
        $expression = $constant->getExpression();
        $expression = str_replace(
            ']',
            ",{$this->getLine()}]",
            $expression
        );
        $constant->setExpression($expression);

        return $constant;
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

    private function createNew(): PhpConstant
    {
        return new PhpConstant(
            'FAKER_DATA_PROVIDERS',
            "[\n{$this->getLine()}]",
            true
        );
    }

    public function updateFakerProviderArrayWithEmbeddableFakerData(
        string $projectRootPath,
        string $embeddableFqn,
        string $entityFqn
    ): void {
        $this->projectRootPath  = $projectRootPath;
        $this->fakerFqn         = str_replace(
            ['\\Traits\\', '\\Has', 'EmbeddableTrait'],
            ['\\FakerData\\', '\\', 'EmbeddableFakerData'],
            $embeddableFqn
        );
        $this->entityFqn        = $entityFqn;
        $this->interfaceFqn     = $this->namespaceHelper->tidy(
            str_replace(
                '\\Traits\\',
                '\\Interfaces\\',
                str_replace('EmbeddableTrait', 'EmbeddableInterface', $embeddableFqn)
            )
        );
        $this->abstractTestPath = $this->projectRootPath . '/tests/Entities/AbstractEntityTest.php';
        $test                   = PhpClass::fromFile($this->abstractTestPath);
        $this->newPropertyConst = 'PROP_' . $this->codeHelper->consty(
            substr($this->namespaceHelper->basename($embeddableFqn), 3, -5)
        );
        try {
            $constant = $this->updateExisting($test);
        } catch (InvalidArgumentException $e) {
            $constant = $this->createNew();
        }
        $test->setConstant($constant);
        $this->codeHelper->generate(
            $test,
            $this->abstractTestPath,
            new class implements PostProcessorInterface
            {
                public function __invoke(string $generated): string
                {
                    return str_replace('// phpcs:enable', '', $generated);
                }
            }
        );
    }
}

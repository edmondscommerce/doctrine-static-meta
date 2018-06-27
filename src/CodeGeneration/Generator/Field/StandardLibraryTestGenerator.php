<?php declare(strict_types=1);
/**
 * When working on the standard library fields, this will generate a skeleton test for any fields you generate
 */

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;

class StandardLibraryTestGenerator
{
    private const FIELDS_PATH = __DIR__.'/../../../Entity/Fields/Traits';

    private const TESTS_PATH = __DIR__.'/../../../../tests/functional/Entity/Fields/Traits';

    private const FIELDS_FQN_BASE = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\';

    private const TEST_TEMPLATE = <<<PHP
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\FOLDER;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\FOLDER\__CLASSY__FieldInterface;

class __CLASSY__FieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/__CLASSY__FieldTraitTest/';
    protected const TEST_FIELD_FQN =   __CLASSY__FieldTrait::class;
    protected const TEST_FIELD_PROP =  __CLASSY__FieldInterface::PROP___CONSTY__;
    protected const TEST_FIELD_DEFAULT = __CLASSY__FieldInterface::DEFAULT___CONSTY__;
}

PHP;
    /**
     * @var CodeHelper
     */
    protected $codeHelper;
    /**
     * @var PathHelper
     */
    protected $pathHelper;

    public function __construct(
        CodeHelper $codeHelper,
        PathHelper $pathHelper
    ) {
        $this->codeHelper = $codeHelper;
        $this->pathHelper = $pathHelper;
    }

    /**
     * @param string $path
     *
     * @return \RecursiveIteratorIterator|\SplFileInfo[]
     */
    private function getIteratorForPath(string $path): \RecursiveIteratorIterator
    {
        $path = \realpath($path);
        if (false === $path) {
            throw new \InvalidArgumentException($path.' does not exist');
        }

        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );
    }

    public function assertTestExistsForField(\ReflectionClass $fieldReflection): void
    {
        $fieldFqn = $fieldReflection->getName();
        $testFqn  = $fieldFqn.'Test';
        try {
            new \ReflectionClass($testFqn);
        } catch (\ReflectionException $e) {
            $this->createTestForField($fieldReflection);
        }
    }

    private function createTestForField(\ReflectionClass $fieldReflection): void
    {
        $fieldName   = str_replace('FieldTrait', '', $fieldReflection->getShortName());
        $contents    = \str_replace(
            [
                'FOLDER',
                '__CLASSY__',
                '__CONSTY__',
            ],
            [
                $this->getFolder($fieldReflection),
                $this->codeHelper->classy($fieldName),
                $this->codeHelper->consty($fieldName),
            ],
            self::TEST_TEMPLATE
        );
        $pathForTest = $this->getPathForTest($fieldReflection);
        $this->pathHelper->ensurePathExists(\dirname($pathForTest));
        \file_put_contents($pathForTest, $contents);
    }

    private function getPathForTest(\ReflectionClass $fieldReflection): string
    {
        return self::TESTS_PATH.'/'.$this->getFolder($fieldReflection).'/'.$fieldReflection->getShortName().'Test.php';
    }

    private function getFolder(\ReflectionClass $fieldReflection): string
    {
        $exp = explode('\\', $fieldReflection->getNamespaceName());

        return end($exp);
    }

    /**
     * @return \Generator|\ReflectionClass[]
     * @throws \ReflectionException
     */
    public function getFields(): \Generator
    {
        $iterator = $this->getIteratorForPath(self::FIELDS_PATH);
        foreach ($iterator as $info) {
            if (false === $info->isFile()) {
                continue;
            }
            yield $this->getFieldReflectionFromFileInfo($info);
        }
    }

    private function getFieldReflectionFromFileInfo(\SplFileInfo $info): \ReflectionClass
    {
        $class        = $info->getBasename('.php');
        $pathExploded = explode('/', $info->getPath());
        $folder       = end($pathExploded);
        $fqn          = self::FIELDS_FQN_BASE."$folder\\$class";

        return new \ReflectionClass($fqn);
    }
}

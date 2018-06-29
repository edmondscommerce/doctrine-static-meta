<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpConstant;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ArchetypeFieldGenerator
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ArchetypeFieldGenerator
{
    /**
     * @var string
     */
    protected $fieldFqn;
    /**
     * @var string
     */
    protected $traitPath;
    /**
     * @var string
     */
    protected $interfacePath;
    /**
     * @var \ReflectionClass
     */
    protected $archetypeFieldTrait;
    /**
     * @var \ReflectionClass
     */
    protected $archetypeFieldInterface;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var string
     */
    protected $projectRootNamespace;
    /**
     * @var CodeHelper
     */
    protected $codeHelper;
    /**
     * @var FindAndReplaceHelper
     */
    protected $findAndReplaceHelper;

    /**
     * ArchetypeFieldGenerator constructor.
     *
     * @param Filesystem           $filesystem
     * @param NamespaceHelper      $namespaceHelper
     * @param CodeHelper           $codeHelper
     * @param FindAndReplaceHelper $findAndReplaceHelper
     */
    public function __construct(
        Filesystem $filesystem,
        NamespaceHelper $namespaceHelper,
        CodeHelper $codeHelper,
        FindAndReplaceHelper $findAndReplaceHelper
    ) {
        $this->filesystem           = $filesystem;
        $this->namespaceHelper      = $namespaceHelper;
        $this->codeHelper           = $codeHelper;
        $this->findAndReplaceHelper = $findAndReplaceHelper;
    }

    /**
     * @param string $fieldFqn
     * @param string $traitPath
     * @param string $interfacePath
     * @param string $archetypeFieldTraitFqn
     * @param string $projectRootNamespace
     *
     * @return string
     * @throws \ReflectionException
     */
    public function createFromArchetype(
        string $fieldFqn,
        string $traitPath,
        string $interfacePath,
        string $archetypeFieldTraitFqn,
        string $projectRootNamespace
    ): string {
        $this->fieldFqn                = $fieldFqn;
        $this->traitPath               = $traitPath;
        $this->interfacePath           = $interfacePath;
        $this->archetypeFieldTrait     = new \ReflectionClass($archetypeFieldTraitFqn);
        $this->archetypeFieldInterface = $this->getArchetypeInterfaceReflection();
        $this->projectRootNamespace    = $projectRootNamespace;
        $this->copyTrait();
        $this->copyInterface();
        $this->copyFakerProvider();
        $this->addFakerProviderToArray();

        return $this->fieldFqn;
    }

    private function getArchetypeInterfaceReflection(): \ReflectionClass
    {
        $interfaceFqn = \str_replace(
            '\\Fields\\Traits\\',
            '\\Fields\\Interfaces\\',
            $this->namespaceHelper->cropSuffix(
                $this->archetypeFieldTrait->getName(),
                'Trait'
            ).'Interface'
        );

        return new \ReflectionClass($interfaceFqn);
    }


    private function getArchetypeFqnRoot(): string
    {
        return \substr(
                   $this->archetypeFieldInterface->getNamespaceName(),
                   0,
                   \strpos($this->archetypeFieldInterface->getNamespaceName(), '\\Entity\\Fields\\Interfaces')
               ).'\\Entity\\Fields';
    }

    private function getArchetypeSubNamespace(): string
    {
        $archetypeTraitFqn = $this->archetypeFieldTrait->getName();
        switch (true) {
            case false !== strpos($archetypeTraitFqn, 'EdmondsCommerce\\DoctrineStaticMeta'):
                $archetypeRootNs = 'EdmondsCommerce\\DoctrineStaticMeta';
                break;
            case false !== strpos($archetypeTraitFqn, $this->projectRootNamespace):
                $archetypeRootNs = $this->projectRootNamespace;
                break;
            default:
                throw new \RuntimeException('Failed finding the archetype root NS in '.__METHOD__);
        }
        list(
            $className,
            ,
            $subDirectories
            ) = $this->namespaceHelper->parseFullyQualifiedName(
            $archetypeTraitFqn,
            'src',
            $archetypeRootNs
        );
        array_shift($subDirectories);
        $subNamespaceParts = [];
        foreach ($subDirectories as $subDirectory) {
            if ($subDirectory === $className) {
                break;
            }
            if ('Traits' === $subDirectory) {
                $subDirectory = '(Traits|Interfaces|FakerData)';
            }
            $subNamespaceParts[] = $subDirectory;
        }

        return implode('\\', $subNamespaceParts);
    }

    private function getNewFqnSubNamespace(): string
    {
        list(
            $className,
            ,
            $subDirectories
            ) = $this->namespaceHelper->parseFullyQualifiedName(
            $this->fieldFqn,
            'src',
            $this->projectRootNamespace
        );
        array_shift($subDirectories);
        $subNamespaceParts = [];
        foreach ($subDirectories as $subDirectory) {

            if ($subDirectory === $className) {
                break;
            }
            if ('Traits' === $subDirectory) {
                $subDirectory = '\$1';
            }
            $subNamespaceParts[] = $subDirectory;
        }

        return implode('\\', $subNamespaceParts);

    }

    protected function replaceInPath(string $path): void
    {
        $contents              = file_get_contents($path);
        $archetypePropertyName = $this->getPropertyName($this->archetypeFieldTrait->getShortName());
        $fieldPropertyName     = $this->getPropertyName($this->namespaceHelper->getClassShortName($this->fieldFqn));
        $find                  = [
            '%(namespace|use) +?'.$this->findAndReplaceHelper->escapeSlashesForRegex($this->getArchetypeFqnRoot()).'(?!\\\\FakerData\\\\Abstract)%',
            '%'.$this->findAndReplaceHelper->escapeSlashesForRegex($this->getArchetypeSubNamespace()).'%',
            '%'.$this->codeHelper->classy($archetypePropertyName).'%',
            '%'.$this->codeHelper->consty($archetypePropertyName).'%',
            '%'.$this->codeHelper->propertyIsh($archetypePropertyName).'%',
        ];
        $replace               = [
            '$1 '.$this->namespaceHelper->tidy($this->projectRootNamespace.'\\Entity\\Fields'),
            $this->getNewFqnSubNamespace(),
            $this->codeHelper->classy($fieldPropertyName),
            $this->codeHelper->consty($fieldPropertyName),
            $this->codeHelper->propertyIsh($fieldPropertyName),
        ];

        $replaced = \preg_replace($find, $replace, $contents);
        file_put_contents($path, $replaced);
    }

    protected function getPropertyName(string $fieldTraitFqn): string
    {
        return $this->namespaceHelper->cropSuffix(
            $fieldTraitFqn,
            'FieldTrait'
        );
    }

    protected function copyTrait(): void
    {
        $this->filesystem->copy($this->archetypeFieldTrait->getFileName(), $this->traitPath);
        $this->replaceInPath($this->traitPath);
    }

    protected function copyInterface(): void
    {
        $this->filesystem->copy($this->archetypeFieldInterface->getFileName(), $this->interfacePath);
        $this->replaceInPath($this->interfacePath);
    }

    protected function copyFakerProvider(): void
    {
        $archetypeFakerFqn = str_replace(
            [
                '\\Traits\\',
                'FieldTrait',
            ],
            [
                '\\FakerData\\',
                'FakerData',
            ],
            $this->archetypeFieldTrait->getName()
        );
        if (\class_exists($archetypeFakerFqn)) {
            $archetypeFaker = new \ReflectionClass($archetypeFakerFqn);
            $newFakerPath   = str_replace(
                [
                    '/Traits/',
                    'FieldTrait',
                ],
                [
                    '/FakerData/',
                    'FakerData',
                ],
                $this->traitPath
            );
            $this->filesystem->copy($archetypeFaker->getFileName(), $newFakerPath);
            $this->replaceInPath($newFakerPath);
            $class = PhpClass::fromFile($newFakerPath);
            $class->removeMethod('__invoke');
            $class->removeUseStatement(AbstractFakerDataProvider::class);
            $class->addUseStatement($archetypeFakerFqn);
            $class->setParentClassName($this->namespaceHelper->basename($archetypeFakerFqn));
            foreach ($class->getConstants() as $constant) {
                $class->removeConstant($constant);
            }
            $this->codeHelper->generate($class, $newFakerPath);
        }
    }

    protected function addFakerProviderToArray()
    {
        $newFakerFqn       = \str_replace('\\Traits\\', '\\FakerData\\', $this->fieldFqn)
                             .'FakerDataProvider';
        $newFakerShort     = $this->namespaceHelper->getClassShortName($newFakerFqn);
        $newInterfaceFqn   = \str_replace(
                                 '\\Traits\\',
                                 '\\Interfaces\\',
                                 $this->fieldFqn
                             ).'Interface';
        $newInterfaceShort = $this->namespaceHelper->getClassShortName($newInterfaceFqn);
        $abstractTestPath  = substr(
                                 $this->traitPath,
                                 0,
                                 strpos(
                                     $this->traitPath,
                                     '/src/'
                                 )
                             ).'/tests/Entities/AbstractEntityTest.php';
        $test              = PhpClass::fromFile($abstractTestPath);
        $newPropertyConst  = 'PROP_'.$this->codeHelper->consty($this->namespaceHelper->basename($this->fieldFqn));
        $test->addUseStatement($newFakerFqn);
        $test->addUseStatement($newInterfaceFqn);

        try {
            $constant = $test->getConstant('FAKER_DATA_PROVIDERS');
            $test->removeConstant($constant);
            $expression = $constant->getExpression();
            $expression = \str_replace(
                ']',
                ",\n$newInterfaceShort::$newPropertyConst => $newFakerShort::class\n]",
                $expression
            );
            $constant->setExpression($expression);
        } catch (\InvalidArgumentException $e) {
            $constant = new PhpConstant(
                'FAKER_DATA_PROVIDERS',
                "[\n$newInterfaceShort::$newPropertyConst => $newFakerShort::class\n]",
                true
            );
        }
        $test->setConstant($constant);
        $this->codeHelper->generate($test, $abstractTestPath);
    }
}

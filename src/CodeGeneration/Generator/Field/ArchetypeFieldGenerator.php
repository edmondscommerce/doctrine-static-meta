<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use ts\Reflection\ReflectionClass;
use function class_exists;
use function preg_replace;
use function str_replace;
use function substr;

/**
 * Class ArchetypeFieldGenerator
 *
 * @package  EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @internal - this is only accessed via CodeGeneration\Generator\Field\FieldGenerator
 */
class ArchetypeFieldGenerator
{
    public const ARCHETYPE_FAKER_DATA_PROVIDER_ALIAS = 'ArchetypeFakerDataProvider';

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
     * @var ReflectionClass
     */
    protected $archetypeFieldTrait;
    /**
     * @var ReflectionClass
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
     * @var ReflectionHelper
     */
    protected $reflectionHelper;

    /**
     * ArchetypeFieldGenerator constructor.
     *
     * @param Filesystem           $filesystem
     * @param NamespaceHelper      $namespaceHelper
     * @param CodeHelper           $codeHelper
     * @param FindAndReplaceHelper $findAndReplaceHelper
     * @param ReflectionHelper     $reflectionHelper
     */
    public function __construct(
        Filesystem $filesystem,
        NamespaceHelper $namespaceHelper,
        CodeHelper $codeHelper,
        FindAndReplaceHelper $findAndReplaceHelper,
        ReflectionHelper $reflectionHelper
    ) {
        $this->filesystem           = $filesystem;
        $this->namespaceHelper      = $namespaceHelper;
        $this->codeHelper           = $codeHelper;
        $this->findAndReplaceHelper = $findAndReplaceHelper;
        $this->reflectionHelper     = $reflectionHelper;
    }

    /**
     * @param string $fieldFqn
     * @param string $traitPath
     * @param string $interfacePath
     * @param string $archetypeFieldTraitFqn
     * @param string $projectRootNamespace
     *
     * @return string
     * @throws ReflectionException
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
        $this->archetypeFieldTrait     = new ReflectionClass($archetypeFieldTraitFqn);
        $this->archetypeFieldInterface = $this->getArchetypeInterfaceReflection();
        $this->projectRootNamespace    = $projectRootNamespace;
        $this->copyTrait();
        $this->copyInterface();
        $this->copyFakerProvider();

        return $this->fieldFqn;
    }

    private function getArchetypeInterfaceReflection(): ReflectionClass
    {
        $interfaceFqn = str_replace(
            '\\Fields\\Traits\\',
            '\\Fields\\Interfaces\\',
            $this->namespaceHelper->cropSuffix(
                $this->archetypeFieldTrait->getName(),
                'Trait'
            ) . 'Interface'
        );

        return new ReflectionClass($interfaceFqn);
    }

    protected function copyTrait(): void
    {
        $this->filesystem->copy($this->archetypeFieldTrait->getFileName(), $this->traitPath);
        $this->replaceInPath($this->traitPath);
    }

    protected function replaceInPath(string $path): void
    {
        $contents              = \ts\file_get_contents($path);
        $archetypePropertyName = $this->getPropertyName($this->archetypeFieldTrait->getShortName());
        $fieldPropertyName     = $this->getPropertyName($this->namespaceHelper->getClassShortName($this->fieldFqn));
        $find                  = [
            '%(namespace|use) +?' . $this->findAndReplaceHelper->escapeSlashesForRegex($this->getArchetypeFqnRoot())
            . '(?!\\\\FakerData\\\\Abstract)%',
            '%' . $this->findAndReplaceHelper->escapeSlashesForRegex($this->getArchetypeSubNamespace()) . '%',
            '%(?<!new )(?<!Constraints\\\\)' . $this->codeHelper->classy($archetypePropertyName) . '%',
            '%' . $this->codeHelper->consty($archetypePropertyName) . '%',
            '%' . $this->codeHelper->propertyIsh($archetypePropertyName) . '%',
            '%isIs%',
        ];
        $replace               = [
            '$1 ' . $this->namespaceHelper->tidy($this->projectRootNamespace . '\\Entity\\Fields'),
            $this->getNewFqnSubNamespace(),
            $this->codeHelper->classy($fieldPropertyName),
            $this->codeHelper->consty($fieldPropertyName),
            $this->codeHelper->propertyIsh($fieldPropertyName),
            'is',
        ];

        $replaced = preg_replace($find, $replace, $contents);
        file_put_contents($path, $replaced);
    }

    protected function getPropertyName(string $fieldTraitFqn): string
    {
        return $this->namespaceHelper->cropSuffix(
            $fieldTraitFqn,
            'FieldTrait'
        );
    }

    private function getArchetypeFqnRoot(): string
    {
        return substr(
                   $this->archetypeFieldInterface->getNamespaceName(),
                   0,
                   \ts\strpos($this->archetypeFieldInterface->getNamespaceName(), '\\Entity\\Fields\\Interfaces')
               ) . '\\Entity\\Fields';
    }

    private function getArchetypeSubNamespace(): string
    {
        $archetypeTraitFqn = $this->archetypeFieldTrait->getName();
        switch (true) {
            case \ts\stringContains($archetypeTraitFqn, 'EdmondsCommerce\\DoctrineStaticMeta'):
                $archetypeRootNs = 'EdmondsCommerce\\DoctrineStaticMeta';
                break;
            case \ts\stringContains($archetypeTraitFqn, $this->projectRootNamespace):
                $archetypeRootNs = $this->projectRootNamespace;
                break;
            default:
                throw new RuntimeException('Failed finding the archetype root NS in ' . __METHOD__);
        }
        list($className, , $subDirectories) = $this->namespaceHelper->parseFullyQualifiedName(
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
        list(, , $subDirectories) = $this->namespaceHelper->parseFullyQualifiedName(
            $this->fieldFqn,
            'src',
            $this->projectRootNamespace
        );
        array_shift($subDirectories);
        $subNamespaceParts = [];
        foreach ($subDirectories as $subDirectory) {
            if ('Traits' === $subDirectory) {
                $subDirectory = '\$1';
            }
            $subNamespaceParts[] = $subDirectory;
        }

        return implode('\\', $subNamespaceParts);
    }

    protected function copyInterface(): void
    {
        $this->filesystem->copy($this->archetypeFieldInterface->getFileName(), $this->interfacePath);
        $this->replaceInPath($this->interfacePath);
    }

    protected function copyFakerProvider(): void
    {
        $archetypeFakerFqn = $this->reflectionHelper
            ->getFakerProviderFqnFromFieldTraitReflection($this->archetypeFieldTrait);
        if (!class_exists($archetypeFakerFqn)) {
            return;
        }
        $archetypeFaker = new ReflectionClass($archetypeFakerFqn);
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
    }
}

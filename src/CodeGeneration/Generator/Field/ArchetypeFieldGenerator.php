<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
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
     * @param string               $fieldFqn
     * @param string               $traitPath
     * @param string               $interfacePath
     * @param string               $archetypeFieldTraitFqn
     * @param string               $projectRootNamespace
     *
     * @throws \ReflectionException
     */
    public function __construct(
        Filesystem $filesystem,
        NamespaceHelper $namespaceHelper,
        CodeHelper $codeHelper,
        FindAndReplaceHelper $findAndReplaceHelper,
        string $fieldFqn,
        string $traitPath,
        string $interfacePath,
        string $archetypeFieldTraitFqn,
        string $projectRootNamespace
    ) {
        $this->filesystem              = $filesystem;
        $this->namespaceHelper         = $namespaceHelper;
        $this->codeHelper              = $codeHelper;
        $this->findAndReplaceHelper    = $findAndReplaceHelper;
        $this->fieldFqn                = $fieldFqn;
        $this->traitPath               = $traitPath;
        $this->interfacePath           = $interfacePath;
        $this->archetypeFieldTrait     = new \ReflectionClass($archetypeFieldTraitFqn);
        $this->archetypeFieldInterface = $this->getArchetypeInterfaceReflection();
        $this->projectRootNamespace    = $projectRootNamespace;
    }

    public function createFromArchetype(): string
    {
        $this->copyTrait();
        $this->copyInterface();

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

    protected function replaceInPath(string $path): void
    {
        $contents              = file_get_contents($path);
        $archetypePropertyName = $this->getPropertyName($this->archetypeFieldTrait->getShortName());
        $fieldPropertyName     = $this->getPropertyName($this->namespaceHelper->getClassShortName($this->fieldFqn));
        $find                  = [
            '%(namespace|use) +?'.$this->findAndReplaceHelper->escapeSlashesForRegex($this->getArchetypeFqnRoot()).'%',
            '%'.$this->codeHelper->classy($archetypePropertyName).'%',
            '%'.$this->codeHelper->consty($archetypePropertyName).'%',
            '%\$'.$this->codeHelper->propertyIsh($archetypePropertyName).'%',
        ];
        $replace               = [
            '$1 '.$this->namespaceHelper->tidy($this->projectRootNamespace.'\\Entity\\Fields'),
            $this->codeHelper->classy($fieldPropertyName),
            $this->codeHelper->consty($fieldPropertyName),
            '$'.$this->codeHelper->propertyIsh($fieldPropertyName),
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
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Symfony\Component\Filesystem\Filesystem;

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
     * @var string
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
     * ArchetypeFieldGenerator constructor.
     *
     * @param Filesystem      $filesystem
     *
     * @param NamespaceHelper $namespaceHelper
     *
     * @param string          $fieldFqn
     * @param string          $traitPath
     * @param string          $interfacePath
     * @param string          $archetypeFieldTraitFqn
     * @param string          $projectRootNamespace
     *
     * @throws \ReflectionException
     */
    public function __construct(
        Filesystem $filesystem,
        NamespaceHelper $namespaceHelper,
        string $fieldFqn,
        string $traitPath,
        string $interfacePath,
        string $archetypeFieldTraitFqn,
        string $projectRootNamespace
    ) {
        $this->filesystem              = $filesystem;
        $this->namespaceHelper         = $namespaceHelper;
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

    private function escapeSlashesForRegex(string $in)
    {
        return \str_replace('\\', '\\\\', $in);
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
            '%(namespace|use) +?'.$this->escapeSlashesForRegex($this->getArchetypeFqnRoot()).'%',
            '%'.$this->classy($archetypePropertyName).'%',
            '%'.$this->consty($archetypePropertyName).'%',
            '%\$'.$this->propertyIsh($archetypePropertyName).'%',
        ];
        $replace               = [
            '$1 '.$this->namespaceHelper->tidy($this->projectRootNamespace.'\\Entity\\Fields'),
            $this->classy($fieldPropertyName),
            $this->consty($fieldPropertyName),
            '$'.$this->propertyIsh($fieldPropertyName),
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

    protected function propertyIsh(string $name): string
    {
        return lcfirst($this->classy($name));
    }

    protected function classy(string $name): string
    {
        return Inflector::classify($name);
    }

    protected function consty(string $name): string
    {
        return strtoupper(Inflector::tableize($name));
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

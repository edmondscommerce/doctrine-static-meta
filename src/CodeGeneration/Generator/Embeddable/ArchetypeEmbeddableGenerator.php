<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use InvalidArgumentException;
use ReflectionException;
use RuntimeException;
use ts\Reflection\ReflectionClass;

use function array_slice;
use function defined;
use function dirname;
use function preg_match;
use function preg_replace;
use function str_replace;
use function strtolower;
use function strtoupper;
use function substr;

/**
 * Class ArchetypeEmbeddableGenerator
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ArchetypeEmbeddableGenerator extends AbstractGenerator
{
    /**
     * @var string
     */
    private $archetypeObjectClassName;
    /**
     * @var string
     */
    private $archetypeObjectNamespace;
    /**
     * @var array
     */
    private $archetypeObjectSubDirectories;
    /**
     * @var string
     */
    private $archetypeObjectPath;
    /**
     * @var string
     */
    private $archetypeObjectInterfacePath;
    /**
     * @var string
     */
    private $archetypeTraitPath;
    /**
     * @var string
     */
    private $archetypeInterfacePath;
    /**
     * @var string
     */
    private $archetypeObjectFqn;
    /**
     * @var string
     */
    private $archetypeObjectInterfaceFqn;
    /**
     * @var string
     */
    private $archetypeTraitFqn;
    /**
     * @var string
     */
    private $archetypeInterfaceFqn;

    /**
     * @var string
     */
    private $archetypeFakerDataFqn;

    /**
     * @var string
     */
    private $archetypeFakerDataPath;
    /**
     * @var string
     */
    private $archetypeProjectRootNamespace;

    /**
     * @var string
     */
    private $newObjectPath;
    /**
     * @var string
     */
    private $newObjectInterfacePath;
    /**
     * @var string
     */
    private $newTraitPath;
    /**
     * @var string
     */
    private $newInterfacePath;
    /**
     * @var string
     */
    private $newObjectFqn;
    /**
     * @var string
     */
    private $newObjectInterfaceFqn;
    /**
     * @var string
     */
    private $newTraitFqn;
    /**
     * @var string
     */
    private $newInterfaceFqn;
    /**
     * @var string
     */
    private $newObjectClassName;

    /**
     * @var string
     */
    private $newFakerDataFqn;

    /**
     * @var string
     */
    private $newFakerDataPath;

    /**
     * @param string $archetypeEmbeddableObjectFqn - the Fully Qualified Name of the Archetype embeddable object
     * @param string $newEmbeddableObjectClassName - the short class name for your new Embeddable Object
     *
     * @return string - the Fully Qualified Name of the Trait for embedding the new Embeddable in your Entity
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function createFromArchetype(
        string $archetypeEmbeddableObjectFqn,
        string $newEmbeddableObjectClassName
    ): string {
        $this->archetypeObjectFqn = $archetypeEmbeddableObjectFqn;
        $this->newObjectClassName = $newEmbeddableObjectClassName;
        $this->validateArguments();
        $this->setupArchetypeProperties();
        $this->setupNewProperties();
        $this->checkForIssues();
        $this->copyObjectAndInterface();
        $this->copyFiles();

        return $this->newTraitFqn;
    }

    private function validateArguments(): void
    {
        if (!class_exists($this->archetypeObjectFqn)) {
            throw new InvalidArgumentException('The archetype FQN ' . $this->archetypeObjectFqn . ' does not exist');
        }
        if (!$this->getEmbeddableObjectInstance($this->archetypeObjectFqn) instanceof AbstractEmbeddableObject) {
            throw new InvalidArgumentException('The archetype FQN does not seem to be an Embeddable Object');
        }
        if (\ts\stringContains($this->newObjectClassName, '\\')) {
            throw new InvalidArgumentException(
                'New class name should not include any namespace component, it is the class short name only'
            );
        }
        if (0 === preg_match('%^.+?Embeddable$%m', $this->newObjectClassName)) {
            throw new InvalidArgumentException(
                'New class name should end with Embeddable'
            );
        }
    }

    private function getEmbeddableObjectInstance(string $entityFqn): object
    {
        if (false === method_exists($entityFqn, 'create')) {
            throw new InvalidArgumentException($entityFqn . ' does not have the required create method');
        }

        return $entityFqn::create($entityFqn::DEFAULTS);
    }

    /**
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    private function setupArchetypeProperties(): void
    {
        //object
        [
            $this->archetypeObjectClassName,
            $this->archetypeObjectNamespace,
            $this->archetypeObjectSubDirectories
        ] = $this->namespaceHelper->parseFullyQualifiedName(
            $this->archetypeObjectFqn,
            AbstractCommand::DEFAULT_SRC_SUBFOLDER,
            Config::DSM_ROOT_NAMESPACE
        );
        $this->archetypeObjectPath = (new ReflectionClass($this->archetypeObjectFqn))->getFileName();

        //object interface
        $this->archetypeObjectInterfaceFqn  = $this->getObjectInterfaceFqnFromObjectClassAndNamespace(
            $this->archetypeObjectClassName,
            $this->archetypeObjectNamespace
        );
        $this->archetypeObjectInterfacePath = (
        new ReflectionClass($this->archetypeObjectInterfaceFqn)
        )->getFileName();

        //trait
        $this->archetypeTraitFqn  = $this->getTraitFqnFromObjectClassAndNamespace(
            $this->archetypeObjectClassName,
            $this->archetypeObjectNamespace
        );
        $this->archetypeTraitPath = (new ReflectionClass($this->archetypeTraitFqn))->getFileName();

        //interface
        $this->archetypeInterfaceFqn  = $this->getInterfaceFqnFromObjectClassAndNamespace(
            $this->archetypeObjectClassName,
            $this->archetypeObjectNamespace
        );
        $this->archetypeInterfacePath = (
        new ReflectionClass($this->archetypeInterfaceFqn)
        )->getFileName();

        //project
        $this->archetypeProjectRootNamespace = substr(
            $this->archetypeObjectNamespace,
            0,
            \ts\strpos($this->archetypeObjectNamespace, '\Entity\Embed')
        );

        $this->archetypeFakerDataFqn = str_replace(
            ['\\Traits\\', '\\Has', 'EmbeddableTrait'],
            ['\\FakerData\\', '\\', 'EmbeddableFakerData'],
            $this->archetypeTraitFqn
        );

        $this->archetypeFakerDataPath = (new ReflectionClass($this->archetypeFakerDataFqn))->getFileName();
    }

    /**
     * Get the Fully Qualified Name of the interface that is implemented by the embeddable object itself
     *
     * @param string $objectClass
     * @param string $objectNamespace
     *
     * @return string
     */
    private function getObjectInterfaceFqnFromObjectClassAndNamespace(
        string $objectClass,
        string $objectNamespace
    ): string {
        $interface = $objectClass . 'Interface';

        return str_replace(
            'Embeddable\\Objects',
            'Embeddable\\Interfaces\\Objects',
            $objectNamespace
        ) . '\\' . $interface;
    }

    /**
     * Get the Fully Qualified Name of the Trait that is used in the owning Entity to embedded the embeddable
     *
     * @param string $objectClass
     * @param string $objectNamespace
     *
     * @return string
     */
    private function getTraitFqnFromObjectClassAndNamespace(string $objectClass, string $objectNamespace): string
    {

        $trait = 'Has' . $objectClass . 'Trait';

        return str_replace(
            'Embeddable\\Objects',
            'Embeddable\\Traits',
            $objectNamespace
        ) . '\\' . $trait;
    }

    /**
     * Get the Fully Qualified Name of the Interface corresponding to the trait. This is the interface that is
     * implemented by the owning Entity
     *
     * @param string $objectClass
     * @param string $objectNamespace
     *
     * @return string
     */
    private function getInterfaceFqnFromObjectClassAndNamespace(string $objectClass, string $objectNamespace): string
    {
        $interface = 'Has' . $objectClass . 'Interface';

        return str_replace(
            'Embeddable\\Objects',
            'Embeddable\\Interfaces',
            $objectNamespace
        ) . '\\' . $interface;
    }

    private function setupNewProperties(): void
    {
        $this->newObjectFqn = $this->getNewEmbeddableFqnFromClassName($this->newObjectClassName);
        //object
        [$newObjectClass, $newObjectNamespace,] = $this->namespaceHelper->parseFullyQualifiedName(
            $this->newObjectFqn,
            AbstractCommand::DEFAULT_SRC_SUBFOLDER,
            $this->projectRootNamespace
        );
        $this->newObjectPath = $this->getNewPathFromArchetypePath($this->archetypeObjectPath);

        //object interface
        $this->newObjectInterfaceFqn  = $this->getObjectInterfaceFqnFromObjectClassAndNamespace(
            $newObjectClass,
            $newObjectNamespace
        );
        $this->newObjectInterfacePath = $this->getNewPathFromArchetypePath($this->archetypeObjectInterfacePath);

        //trait
        $this->newTraitFqn  = $this->getTraitFqnFromObjectClassAndNamespace(
            $newObjectClass,
            $newObjectNamespace
        );
        $this->newTraitPath = $this->getNewPathFromArchetypePath($this->archetypeTraitPath);

        //interface
        $this->newInterfaceFqn  = $this->getInterfaceFqnFromObjectClassAndNamespace(
            $newObjectClass,
            $newObjectNamespace
        );
        $this->newInterfacePath = $this->getNewPathFromArchetypePath($this->archetypeInterfacePath);

        $this->newFakerDataFqn = str_replace(
            ['\\Traits\\', '\\Has', 'EmbeddableTrait'],
            ['\\FakerData\\', '', 'FakerData'],
            $this->newFakerDataFqn
        );

        $this->newFakerDataPath = $this->getNewPathFromArchetypePath($this->archetypeFakerDataPath);
    }

    /**
     * Get the Fully Qualified name for the new embeddable object based upon the project root names
     *
     * @param string $className
     *
     * @return string
     */
    private function getNewEmbeddableFqnFromClassName(string $className): string
    {
        return $this->namespaceHelper->tidy(
            $this->projectRootNamespace . '\\'
            . implode('\\', array_slice($this->archetypeObjectSubDirectories, 1))
            . '\\' . $className
        );
    }

    /**
     * Calculate the new path by doing a preg replace on the corresponding archetype path
     *
     * @param string $archetypePath
     *
     * @return null|string|string[]
     */
    private function getNewPathFromArchetypePath(string $archetypePath): string
    {
        $rootArchetypePath = substr($archetypePath, 0, \ts\strpos($archetypePath, '/src/Entity/Embeddable'));

        $path = str_replace($rootArchetypePath, $this->pathToProjectRoot, $archetypePath);

        $pattern     = '%^(.*?)/([^/]*?)' . $this->archetypeObjectClassName . '([^/]*?)php$%m';
        $replacement = '$1/$2' . $this->newObjectClassName . '$3php';

        $path = preg_replace($pattern, $replacement, $path, -1, $replacements);
        if (0 === $replacements) {
            throw new RuntimeException('Failed updating the path with regex in ' . __METHOD__);
        }

        return $path;
    }

    private function checkForIssues(): void
    {
        if ($this->newObjectClassName === $this->archetypeObjectClassName) {
            return;
        }
        if (\ts\stringContains($this->newObjectClassName, $this->archetypeObjectClassName)) {
            throw new InvalidArgumentException(
                'Please do not generate an embeddable that is simply a prefix of the archetype'
            );
        }
    }

    private function copyObjectAndInterface(): void
    {
        $this->pathHelper->ensurePathExists(dirname($this->newObjectPath));
        $this->fileSystem->copy($this->archetypeObjectPath, $this->newObjectPath);
        $this->replaceInPath($this->newObjectPath);
        $this->pathHelper->ensurePathExists(dirname($this->newObjectInterfacePath));
        $this->fileSystem->copy($this->archetypeObjectInterfacePath, $this->newObjectInterfacePath);
        $this->replaceInPath($this->newObjectInterfacePath);
    }

    private function replaceInPath(string $newPath): void
    {
        $contents = \ts\file_get_contents($newPath);
        $find     = [
            '%' . $this->codeHelper->classy($this->archetypeObjectClassName) . '%',
            '%' . $this->codeHelper->consty($this->archetypeObjectClassName) . '%',
            '%' . $this->codeHelper->propertyIsh($this->archetypeObjectClassName) . '%',
            '%' . $this->getMetaForMethodName($this->archetypeObjectClassName) . '%',
            '%' . $this->getInitMethodName($this->archetypeObjectClassName) . '%',
            '%' . $this->getColumnPrefixConst($this->archetypeObjectClassName) . '%',
            '%' . $this->getColumnPrefix($this->archetypeObjectClassName) . '%',
            '%(namespace|use) +?' . $this->findAndReplaceHelper->escapeSlashesForRegex(
                $this->archetypeProjectRootNamespace . '\\Entity\\Embeddable\\(?!.+?\\Abstract)'
            ) . '%',
        ];
        $replace  = [
            $this->codeHelper->classy($this->newObjectClassName),
            $this->codeHelper->consty($this->newObjectClassName),
            $this->codeHelper->propertyIsh($this->newObjectClassName),
            $this->getMetaForMethodName($this->newObjectClassName),
            $this->getInitMethodName($this->newObjectClassName),
            $this->getColumnPrefixConst($this->newObjectClassName),
            $this->getColumnPrefix($this->newObjectClassName),
            '$1 ' . $this->namespaceHelper->tidy($this->projectRootNamespace . '\\Entity\\Embeddable\\'),
        ];
        $updated  = $contents;
        foreach ($find as $key => $fnd) {
            $updated = preg_replace($fnd, $replace[$key], $updated /*, -1, $count*/);
        }
        file_put_contents($newPath, $updated);
    }

    private function getMetaForMethodName(string $embeddableObjectClassName): string
    {
        return 'metaFor' . str_replace('Embeddable', '', $embeddableObjectClassName);
    }

    private function getInitMethodName(string $embeddableObjectClassName): string
    {
        return 'init' . str_replace('Embeddable', '', $embeddableObjectClassName);
    }

    private function getColumnPrefixConst(string $embeddableObjectClassName): string
    {
        return 'COLUMN_PREFIX_'
               . strtoupper(
                   str_replace(
                       '_EMBEDDABLE',
                       '',
                       $this->codeHelper->consty($embeddableObjectClassName)
                   )
               );
    }

    private function getColumnPrefix(string $embeddableObjectClassName): string
    {
        return strtolower(
            str_replace(
                '_EMBEDDABLE',
                '',
                $this->codeHelper->consty($embeddableObjectClassName)
            )
        ) . '_';
    }

    private function copyFiles(): void
    {
        $this->pathHelper->ensurePathExists(dirname($this->newTraitPath));
        $this->fileSystem->copy($this->archetypeTraitPath, $this->newTraitPath);
        $this->replaceInPath($this->newTraitPath);

        $this->pathHelper->ensurePathExists(dirname($this->newInterfacePath));
        $this->fileSystem->copy($this->archetypeInterfacePath, $this->newInterfacePath);
        $this->replaceInPath($this->newInterfacePath);

        $this->pathHelper->ensurePathExists(dirname($this->newFakerDataPath));
        $this->fileSystem->copy($this->archetypeFakerDataPath, $this->newFakerDataPath);
        $this->replaceInPath($this->newFakerDataPath);
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Class NamespaceHelper
 *
 * Pure functions for working with namespaces and to calculate namespaces
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class NamespaceHelper
{
    /**
     * Get the basename of a namespace
     *
     * @param string $namespace
     *
     * @return string
     */
    public function basename(string $namespace): string
    {
        $strrpos = strrpos($namespace, '\\');
        if (false === $strrpos) {
            return $namespace;
        }

        return $this->tidy(substr($namespace, $strrpos + 1));
    }

    public function tidy(string $namespace): string
    {
        #remove repeated separators
        $namespace = preg_replace(
            '#'.'\\\\'.'+#',
            '\\',
            $namespace
        );

        return $namespace;
    }

    /**
     * Work out the entity namespace root from a single entity reflection object.
     *
     * @param \ReflectionClass $entityReflection
     *
     * @return string
     */
    public function getEntityNamespaceRootFromEntityReflection(
        \ReflectionClass $entityReflection
    ): string {
        return $this->tidy(
            $this->getNamespaceRootToDirectoryFromFqn(
                $entityReflection->getName(),
                AbstractGenerator::ENTITIES_FOLDER_NAME
            )
        );
    }

    /**
     * Get the namespace root up to and including a specified directory
     *
     * @param string $fqn
     * @param string $directory
     *
     * @return null|string
     */
    public function getNamespaceRootToDirectoryFromFqn(string $fqn, string $directory): ?string
    {
        $strPos = \strrpos(
            $fqn,
            $directory
        );
        if (false !== $strPos) {
            return $this->tidy(\substr($fqn, 0, $strPos + \strlen($directory)));
        }

        return null;
    }

    /**
     * Get the sub path for an Entity file, start from the Entities path - normally `/path/to/project/src/Entities`
     *
     * @param string $entityFqn
     *
     * @return string
     */
    public function getEntityFileSubPath(
        string $entityFqn
    ): string {
        return $this->getEntitySubPath($entityFqn).'.php';
    }

    /**
     * Get the folder structure for an Entity, start from the Entities path - normally `/path/to/project/src/Entities`
     *
     * This is not the path to the file, but the sub path of directories for storing entity related items.
     *
     * @param string $entityFqn
     *
     * @return string
     */
    public function getEntitySubPath(
        string $entityFqn
    ): string {
        $entityPath = str_replace(
            '\\',
            '/',
            $this->getEntitySubNamespace($entityFqn)
        );

        return '/'.$entityPath;
    }

    /**
     * Get the Namespace for an Entity, start from the Entities Fully Qualified Name base - normally
     * `\My\Project\Entities\`
     *
     * @param string $entityFqn
     *
     * @return string
     */
    public function getEntitySubNamespace(
        string $entityFqn
    ): string {
        return $this->tidy(
            \substr(
                $entityFqn,
                \strrpos(
                    $entityFqn,
                    '\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\'
                )
                + \strlen('\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\')
            )
        );
    }

    /**
     * Get the Fully Qualified Namespace root for Traits for the specified Entity
     *
     * @param string $entityFqn
     *
     * @return string
     */
    public function getTraitsNamespaceForEntity(
        string $entityFqn
    ): string {
        $traitsNamespace = $this->getProjectNamespaceRootFromEntityFqn($entityFqn)
                           .'\\'.AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME
                           .'\\'.$this->getEntitySubNamespace($entityFqn)
                           .'\\Traits';

        return $traitsNamespace;
    }

    /**
     * Use the fully qualified name of two Entities to calculate the Project Namespace Root
     *
     * - note: this assumes a single namespace level for entities, eg `Entities`
     *
     * @param string $entityFqn
     *
     * @return string
     */
    public function getProjectNamespaceRootFromEntityFqn(string $entityFqn): string
    {
        return $this->tidy(
            \substr(
                $entityFqn,
                0,
                \strrpos(
                    $entityFqn,
                    '\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\'
                )
            )
        );
    }

    /**
     * Get the Fully Qualified Namespace for the "HasEntities" interface for the specified Entity
     *
     * @param string $entityFqn
     *
     * @return string
     */
    public function getHasPluralInterfaceFqnForEntity(
        string $entityFqn
    ): string {
        $interfaceNamespace = $this->getInterfacesNamespaceForEntity($entityFqn);

        return $interfaceNamespace.'\\Has'.ucfirst($entityFqn::getPlural());
    }

    /**
     * Get the Fully Qualified Namespace root for Interfaces for the specified Entity
     *
     * @param string $entityFqn
     *
     * @return string
     */
    public function getInterfacesNamespaceForEntity(
        string $entityFqn
    ): string {
        $interfacesNamespace = $this->getProjectNamespaceRootFromEntityFqn($entityFqn)
                               .'\\'.AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME
                               .'\\'.$this->getEntitySubNamespace($entityFqn)
                               .'\\Interfaces';

        return $this->tidy($interfacesNamespace);
    }

    /**
     * Get the Fully Qualified Namespace for the "HasEntity" interface for the specified Entity
     *
     * @param string $entityFqn
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getHasSingularInterfaceFqnForEntity(
        string $entityFqn
    ): string {
        try {
            $interfaceNamespace = $this->getInterfacesNamespaceForEntity($entityFqn);

            return $interfaceNamespace.'\\Has'.ucfirst($entityFqn::getSingular());
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the Fully Qualified Namespace for the Relation Trait for a specific Entity and hasType
     *
     * @param string      $hasType
     * @param string      $ownedEntityFqn
     * @param string|null $projectRootNamespace
     * @param string      $srcFolder
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getOwningTraitFqn(
        string $hasType,
        string $ownedEntityFqn,
        ?string $projectRootNamespace = null,
        string $srcFolder = AbstractCommand::DEFAULT_SRC_SUBFOLDER
    ): string {
        try {
            $ownedHasName = $this->getOwnedHasName($hasType, $ownedEntityFqn);
            if (null === $projectRootNamespace) {
                $projectRootNamespace = $this->getProjectRootNamespaceFromComposerJson($srcFolder);
            }
            list($ownedClassName, , $ownedSubDirectories) = $this->parseFullyQualifiedName(
                $ownedEntityFqn,
                $srcFolder,
                $projectRootNamespace
            );
            $traitSubDirectories = \array_slice($ownedSubDirectories, 2);
            $owningTraitFqn      = $this->getOwningRelationsRootFqn(
                $projectRootNamespace,
                $traitSubDirectories
            );
            $owningTraitFqn      .= $ownedClassName.'\\Traits\\Has'.$ownedHasName
                                    .'\\Has'.$ownedHasName.$this->stripPrefixFromHasType($hasType);

            return $this->tidy($owningTraitFqn);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Based on the $hasType, we calculate exactly what type of `Has` we have
     *
     * @param string $hasType
     * @param string $ownedEntityFqn
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getOwnedHasName(string $hasType, string $ownedEntityFqn): string
    {
        if (\in_array(
            $hasType,
            RelationsGenerator::HAS_TYPES_PLURAL,
            true
        )) {
            return ucfirst(MappingHelper::getPluralForFqn($ownedEntityFqn));
        }

        return ucfirst(MappingHelper::getSingularForFqn($ownedEntityFqn));
    }

    /**
     * Read src autoloader from composer json
     *
     * @param string $dirForNamespace
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getProjectRootNamespaceFromComposerJson(
        string $dirForNamespace = 'src'
    ): string {
        try {
            $dirForNamespace = trim($dirForNamespace, '/');
            $json            = json_decode(
                file_get_contents(Config::getProjectRootDirectory().'/composer.json'),
                true
            );
            /**
             * @var string[][][][] $json
             */
            if (isset($json['autoload']['psr-4'])) {
                foreach ($json['autoload']['psr-4'] as $namespace => $dirs) {
                    foreach ($dirs as $dir) {
                        $dir = trim($dir, '/');
                        if ($dir === $dirForNamespace) {
                            return $this->tidy(rtrim($namespace, '\\'));
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
        throw new DoctrineStaticMetaException('Failed to find psr-4 namespace root');
    }

    /**
     * From the fully qualified name, parse out:
     *  - class name,
     *  - namespace
     *  - the namespace parts not including the project root namespace
     *
     * @param string $fqn
     *
     * @param string $srcOrTestSubFolder
     *
     * @param string $projectRootNamespace
     *
     * @return array [$className,$namespace,$subDirectories]
     * @throws DoctrineStaticMetaException
     */
    public function parseFullyQualifiedName(
        string $fqn,
        string $srcOrTestSubFolder,
        string $projectRootNamespace = null
    ): array {
        try {
            if (null === $projectRootNamespace) {
                $projectRootNamespace = $this->getProjectRootNamespaceFromComposerJson($srcOrTestSubFolder);
            }
            $fqnParts       = explode('\\', $fqn);
            $className      = array_pop($fqnParts);
            $namespace      = implode('\\', $fqnParts);
            $rootParts      = explode('\\', $projectRootNamespace);
            $subDirectories = [];
            foreach ($fqnParts as $k => $fqnPart) {
                if (isset($rootParts[$k]) && $rootParts[$k] === $fqnPart) {
                    continue;
                }
                $subDirectories[] = $fqnPart;
            }
            array_unshift($subDirectories, $srcOrTestSubFolder);

            return [
                $className,
                $this->tidy($namespace),
                $subDirectories,
            ];
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the Namespace root for Entity Relations
     *
     * @param string $projectRootNamespace
     * @param array  $subDirectories
     *
     * @return string
     */
    public function getOwningRelationsRootFqn(
        string $projectRootNamespace,
        array $subDirectories
    ): string {
        $relationsRootFqn = $projectRootNamespace
                            .'\\'.AbstractGenerator::ENTITY_RELATIONS_FOLDER_NAME.'\\';
        if (count($subDirectories) > 0) {
            $relationsRootFqn .= implode('\\', $subDirectories).'\\';
        }

        return $this->tidy($relationsRootFqn);
    }

    /**
     * Normalise a has type, removing prefixes that are not required
     *
     * Inverse hasTypes use the standard template without the prefix
     * The exclusion ot this are the ManyToMany and OneToOne relations
     *
     * @param string $hasType
     *
     * @return string
     */
    public function stripPrefixFromHasType(
        string $hasType
    ): string {
        foreach ([
                     RelationsGenerator::INTERNAL_TYPE_MANY_TO_MANY,
                     RelationsGenerator::INTERNAL_TYPE_ONE_TO_ONE,
                 ] as $noStrip) {
            if (false !== strpos($hasType, $noStrip)) {
                return $hasType;
            }
        }

        foreach ([
                     RelationsGenerator::INTERNAL_TYPE_ONE_TO_MANY,
                     RelationsGenerator::INTERNAL_TYPE_MANY_TO_ONE,
                 ] as $stripAll) {
            if (false !== strpos($hasType, $stripAll)) {
                return str_replace(
                    [
                        RelationsGenerator::PREFIX_OWNING,
                        RelationsGenerator::PREFIX_INVERSE,
                    ],
                    '',
                    $hasType
                );
            }
        }

        return str_replace(
            [
                RelationsGenerator::PREFIX_INVERSE,
            ],
            '',
            $hasType
        );
    }

    /**
     * Get the Fully Qualified Namespace for the Relation Interface for a specific Entity and hasType
     *
     * @param string      $hasType
     * @param string      $ownedEntityFqn
     * @param string|null $projectRootNamespace
     * @param string      $srcFolder
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getOwningInterfaceFqn(
        string $hasType,
        string $ownedEntityFqn,
        string $projectRootNamespace = null,
        string $srcFolder = AbstractCommand::DEFAULT_SRC_SUBFOLDER
    ): string {
        try {
            $ownedHasName = $this->getOwnedHasName($hasType, $ownedEntityFqn);
            if (null === $projectRootNamespace) {
                $projectRootNamespace = $this->getProjectRootNamespaceFromComposerJson($srcFolder);
            }
            list($ownedClassName, , $ownedSubDirectories) = $this->parseFullyQualifiedName(
                $ownedEntityFqn,
                $srcFolder,
                $projectRootNamespace
            );
            $interfaceSubDirectories = \array_slice($ownedSubDirectories, 2);
            $owningInterfaceFqn      = $this->getOwningRelationsRootFqn(
                $projectRootNamespace,
                $interfaceSubDirectories
            );
            $owningInterfaceFqn      .= '\\'.$ownedClassName.'\\Interfaces\\Has'.$ownedHasName;

            return $this->tidy($owningInterfaceFqn);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Class NamespaceHelper
 *
 * Pure functions for working with namespaces and to calculate namespaces
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class NamespaceHelper
{
    /**
     * Use the fully qualified name of two Entities, Interfaces or Traits to calculate the Entity Namespace Root
     *
     * @param string $entity1Fqn
     * @param string $entity2Fqn
     *
     * @return string
     */
    public function getEntityNamespaceRootFromTwoEntityFqns(string $entity1Fqn, string $entity2Fqn): string
    {
        $entity1parts = explode('\\', $entity1Fqn);
        $entity2parts = explode('\\', $entity2Fqn);
        $intersect    = [];
        foreach ($entity1parts as $k => $part) {
            if (isset($entity2parts[$k]) && $entity2parts[$k] === $part) {
                $intersect[] = $part;
                continue;
            }
            break;
        }

        return implode('\\', $intersect);
    }

    /**
     * Use the fully qualified name of two Entities, Interfaces or Traits to calculate the Project Namespace Root
     *
     * - note: this assumes a single namespace level for entities, eg `Entities`
     *
     * @param string $entity1Fqn
     * @param string $entity2Fqn
     *
     * @return string
     */
    public function getProjectNamespaceRootFromTwoEntityFqns(string $entity1Fqn, string $entity2Fqn): string
    {
        $entityRootNamespace = $this->getEntityNamespaceRootFromTwoEntityFqns($entity1Fqn, $entity2Fqn);

        return substr($entityRootNamespace, 0, strrpos($entityRootNamespace, '\\'));
    }

    /**
     * Based on the $hasType, we calculate exactly what type of `Has` we have
     *
     * @param string $hasType
     * @param string $ownedEntityFqn
     *
     * @return string
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
                $namespace,
                $subDirectories,
            ];
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
        }
    }

    /**
     * Work out the entity namespace root from a single entity reflection object.
     *
     * The object must have at least one association unless we pass in a default which it will then split on,
     * if its in there
     *
     * @param \ReflectionClass $entityReflection
     *
     * @param null             $defaultEntitiesDirectory
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getEntityNamespaceRootFromEntityReflection(
        \ReflectionClass $entityReflection,
        $defaultEntitiesDirectory = null
    ): string {
        $interfaces = $entityReflection->getInterfaces();
        if (count($interfaces) < 2) {
            if (null !== $defaultEntitiesDirectory && false !== strpos(
                $entityReflection->getName(),
                $defaultEntitiesDirectory
            )) {
                return explode($defaultEntitiesDirectory, $entityReflection->getName())[0];
            }
            throw new DoctrineStaticMetaException('the entity '.$entityReflection->getShortName().' does not have interfaces implemented');
        }
        foreach ($interfaces as $interface) {
            if (0 === strpos($interface->getShortName(), 'Has')) {
                $methods = $interface->getMethods(\ReflectionMethod::IS_STATIC);
                foreach ($methods as $method) {
                    if ($method instanceof \ReflectionMethod) {
                        $method = $method->getName();
                    }
                    if (0 === strpos($method, UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_META)) {
                        return $this->getEntityNamespaceRootFromTwoEntityFqns(
                            $entityReflection->getName(),
                            $interface->getName()
                        );
                    }
                }
            }
        }
        throw new DoctrineStaticMetaException(
            'Failed to find the entity namespace root from the entity '
            .$entityReflection->getName()
        );
    }

    /**
     * Get the Namespace for an Entity, start from the Entities Fully Qualified Name base - normally
     * `\My\Project\Entities\`
     *
     * @param string $entityFqn
     * @param string $entitiesRootFqn
     *
     * @return string
     */
    public function getEntitySubNamespace(
        string $entityFqn,
        string $entitiesRootFqn
    ): string {
        return substr($entityFqn, strlen($entitiesRootFqn) + 1);
    }

    /**
     * Get the folder structure for an Entity, start from the Entities path - normally `/path/to/project/src/Entities`
     *
     * This is not the path to the file, but the sub path of directories for storing entity related items.
     *
     * @param string $entityFqn
     * @param string $entitiesRootFqn
     *
     * @return string
     */
    public function getEntitySubPath(
        string $entityFqn,
        string $entitiesRootFqn
    ): string {
        $entityPath = str_replace(
            '\\',
            '/',
            $this->getEntitySubNamespace($entityFqn, $entitiesRootFqn)
        );

        return '/'.$entityPath;
    }

    /**
     * Get the sub path for an Entity file, start from the Entities path - normally `/path/to/project/src/Entities`
     *
     * @param string $entityFqn
     * @param string $entitiesRootFqn
     *
     * @return string
     */
    public function getEntityFileSubPath(
        string $entityFqn,
        string $entitiesRootFqn
    ): string {
        return $this->getEntitySubPath($entityFqn, $entitiesRootFqn).'.php';
    }

    /**
     * Get the Fully Qualified Namespace root for Interfaces for the specified Entity
     *
     * @param string $entityFqn
     * @param string $entitiesRootNamespace
     *
     * @return string
     */
    public function getInterfacesNamespaceForEntity(
        string $entityFqn,
        string $entitiesRootNamespace
    ): string {
        $interfacesNamespace = $entitiesRootNamespace.'\\Relations\\'
                            .$this->getEntitySubNamespace(
                                $entityFqn,
                                $entitiesRootNamespace
                            )
                               .'\\Interfaces';

        return $interfacesNamespace;
    }

    /**
     * Get the Fully Qualified Namespace root for Traits for the specified Entity
     *
     * @param string $entityFqn
     * @param string $entitiesRootNamespace
     *
     * @return string
     */
    public function getTraitsNamespaceForEntity(
        string $entityFqn,
        string $entitiesRootNamespace
    ): string {
        $traitsNamespace = $entitiesRootNamespace.'\\Relations\\'
                        .$this->getEntitySubNamespace(
                            $entityFqn,
                            $entitiesRootNamespace
                        )
                           .'\\Traits';

        return $traitsNamespace;
    }

    /**
     * Get the Fully Qualified Namespace for the "HasEntities" interface for the specified Entity
     *
     * @param string $entityFqn
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function getHasPluralInterfaceFqnForEntity(
        string $entityFqn
    ): string {
        $entityReflection      = new\ReflectionClass($entityFqn);
        $entitiesRootNamespace = $this->getEntityNamespaceRootFromEntityReflection($entityReflection);
        $interfaceNamespace    = $this->getInterfacesNamespaceForEntity($entityFqn, $entitiesRootNamespace);

        return $interfaceNamespace.'\\Has'.ucfirst($entityFqn::getPlural());
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
            $entityReflection      = new\ReflectionClass($entityFqn);
            $entitiesRootNamespace = $this->getEntityNamespaceRootFromEntityReflection($entityReflection);
            $interfaceNamespace    = $this->getInterfacesNamespaceForEntity($entityFqn, $entitiesRootNamespace);

            return $interfaceNamespace.'\\Has'.ucfirst($entityFqn::getSingular());
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
        }
    }


    /**
     * Read src autoloader from composer json
     *
     * @param string $dirForNamespace
     *
     * @return string
     * @throws DoctrineStaticMetaException
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
                            return rtrim($namespace, '\\');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
        }
        throw new DoctrineStaticMetaException('Failed to find psr-4 namespace root');
    }

    /**
     * Get the Fully Qualified Namespace for the Relation Trait for a specific Entity and hasType
     *
     * @param string      $hasType
     * @param string      $ownedEntityFqn
     * @param string|null $projectRootNamespace
     * @param string      $srcFolder
     * @param string      $entitiesFolderName
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getOwningTraitFqn(
        string $hasType,
        string $ownedEntityFqn,
        string $projectRootNamespace = null,
        string $srcFolder = AbstractCommand::DEFAULT_SRC_SUBFOLDER,
        string $entitiesFolderName = AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER
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
                $entitiesFolderName,
                $traitSubDirectories
            );
            $owningTraitFqn      .= $ownedClassName.'\\Traits\\Has'.$ownedHasName
                                    .'\\Has'.$ownedHasName.$this->stripPrefixFromHasType($hasType);

            return $owningTraitFqn;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
        }
    }

    /**
     * Get the Namespace root for Entity Relations
     *
     * @param string $projectRootNamespace
     * @param string $entitiesFolderName
     * @param array  $subDirectories
     *
     * @return string
     */
    public function getOwningRelationsRootFqn(
        string $projectRootNamespace,
        string $entitiesFolderName,
        array $subDirectories
    ): string {
        $relationsRootFqn = $projectRootNamespace
                            .'\\'.$entitiesFolderName
                            .'\\Relations\\';
        if (count($subDirectories) > 0) {
            $relationsRootFqn .= implode('\\', $subDirectories).'\\';
        }

        return $relationsRootFqn;
    }

    /**
     * Get the Fully Qualified Namespace for the Relation Interface for a specific Entity and hasType
     *
     * @param string      $hasType
     * @param string      $ownedEntityFqn
     * @param string|null $projectRootNamespace
     * @param string      $srcFolder
     * @param string      $entitiesFolderName
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getOwningInterfaceFqn(
        string $hasType,
        string $ownedEntityFqn,
        string $projectRootNamespace = null,
        string $srcFolder = AbstractCommand::DEFAULT_SRC_SUBFOLDER,
        string $entitiesFolderName = AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER
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
                $entitiesFolderName,
                $interfaceSubDirectories
            );
            $owningInterfaceFqn      .= '\\'.$ownedClassName.'\\Interfaces\\Has'.$ownedHasName;

            return $owningInterfaceFqn;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
        }
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
}

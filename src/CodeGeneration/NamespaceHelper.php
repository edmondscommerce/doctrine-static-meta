<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

/**
 * Class NamespaceHelper
 *
 * Pure functions for working with namespaces and to calculate namespaces
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration
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
            } else {
                break;
            }
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
        if (in_array(
            $hasType,
            RelationsGenerator::HAS_TYPES_PLURAL
        )) {
            $ownedHasName = ucfirst(MappingHelper::getPluralForFqn($ownedEntityFqn));
        } else {
            $ownedHasName = ucfirst(MappingHelper::getSingularForFqn($ownedEntityFqn));
        }
        return $ownedHasName;
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
     * @throws \ReflectionException
     */
    public function parseFullyQualifiedName(string $fqn, string $srcOrTestSubFolder, string $projectRootNamespace = null): array
    {
        if (null === $projectRootNamespace) {
            $projectRootNamespace = $this->getProjectRootNamespaceFromComposerJson($srcOrTestSubFolder);
        }
        $fqnParts       = explode('\\', $fqn);
        $className      = array_pop($fqnParts);
        $namespace      = implode('\\', $fqnParts);
        $rootParts      = explode('\\', $projectRootNamespace);
        $subDirectories = [];
        foreach ($fqnParts as $k => $fqnPart) {
            if (isset($rootParts[$k]) && $rootParts[$k] == $fqnPart) {
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
    }

    /**
     * Work out the entity namespace root from a single entity reflection object.
     *
     * The object must have at least one association.
     *
     * @param \ReflectionClass $entityReflection
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getEntityNamespaceRootFromEntityReflection(\ReflectionClass $entityReflection): string
    {
        $interfaces = $entityReflection->getInterfaces();
        if (count($interfaces) < 2) {
            throw new DoctrineStaticMetaException('the entity ' . $entityReflection->getShortName() . ' does not have interfaces implemented');
        }
        foreach ($interfaces as $interface) {
            if (0 === strpos($interface->getShortName(), 'Has')) {
                $methods = $interface->getMethods(\ReflectionMethod::IS_STATIC);
                foreach ($methods as $method) {
                    if ($method instanceof \ReflectionMethod) {
                        $method = $method->getName();
                    }
                    if (0 === strpos($method, UsesPHPMetaDataInterface::propertyMetaDataMethodPrefix)) {
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
            . $entityReflection->getName()
        );
    }

    public function getEntitySubNamespace(
        string $entityFqn,
        string $entitiesRootFqn
    ): string
    {
        $entitySubFqn = substr($entityFqn, strlen($entitiesRootFqn) + 1);

        return $entitySubFqn;
    }

    public function getEntitySubPath(
        string $entityFqn,
        string $entitiesRootFqn,
        bool $includeFileExtension = true
    ): string
    {
        $entityPath = str_replace(
            '\\',
            '/',
            $this->getEntitySubNamespace($entityFqn, $entitiesRootFqn)
        );
        return '/' . $entityPath . ($includeFileExtension ? '.php' : '');
    }

    public function getInterfacesNamespaceForEntity(
        string $entityFqn,
        string $entitiesRootNamespace
    ): string
    {
        $interfacesNamespace = $entitiesRootNamespace . '\\Relations\\'
            . $this->getEntitySubNamespace(
                $entityFqn,
                $entitiesRootNamespace
            )
            . '\\Interfaces';
        return $interfacesNamespace;
    }

    public function getTraitsNamespaceForEntity(
        string $entityFqn,
        string $entitiesRootNamespace
    ): string
    {
        $traitsNamespace = $entitiesRootNamespace . '\\Relations\\'
            . $this->getEntitySubNamespace(
                $entityFqn,
                $entitiesRootNamespace
            )
            . '\\Traits';
        return $traitsNamespace;
    }

    public function getHasPluralInterfaceFqnForEntity(string $entityFqn): string
    {
        $entityReflection      = new\ReflectionClass($entityFqn);
        $entitiesRootNamespace = $this->getEntityNamespaceRootFromEntityReflection($entityReflection);
        $interfaceNamespace    = $this->getInterfacesNamespaceForEntity($entityFqn, $entitiesRootNamespace);
        return $interfaceNamespace . '\\Has' . ucfirst($entityFqn::getPlural());
    }

    public function getHasSingularInterfaceFqnForEntity(string $entityFqn): string
    {
        $entityReflection      = new\ReflectionClass($entityFqn);
        $entitiesRootNamespace = $this->getEntityNamespaceRootFromEntityReflection($entityReflection);
        $interfaceNamespace    = $this->getInterfacesNamespaceForEntity($entityFqn, $entitiesRootNamespace);
        return $interfaceNamespace . '\\Has' . ucfirst($entityFqn::getSingular());
    }


    /**
     * Read src autoloader from composer json
     *
     * @param string $dirForNamespace
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function getProjectRootNamespaceFromComposerJson(string $dirForNamespace = 'src'): string
    {
        $dirForNamespace = trim($dirForNamespace, '/');
        $json            = json_decode(
            file_get_contents(Config::getProjectRootDirectory() . '/composer.json'),
            true
        );
        if (isset($json['autoload'])) {
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
        }
        throw new DoctrineStaticMetaException('Failed to find psr-4 namespace root');
    }

    public function getOwningTraitFqn(
        string $hasType,
        string $ownedEntityFqn,
        string $projectRootNamespace = null,
        string $srcFolder = AbstractCommand::DEFAULT_SRC_SUBFOLDER,
        string $entitiesFolderName = AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER
    ): string
    {
        $ownedHasName = $this->getOwnedHasName($hasType, $ownedEntityFqn);
        if (null === $projectRootNamespace) {
            $projectRootNamespace = $this->getProjectRootNamespaceFromComposerJson($srcFolder);
        }
        list($ownedClassName, , $ownedSubDirectories) = $this->parseFullyQualifiedName(
            $ownedEntityFqn,
            $srcFolder,
            $projectRootNamespace
        );
        $traitSubDirectories = array_slice($ownedSubDirectories, 2);
        $owningTraitFqn      = $this->getOwningRelationsRootFqn($projectRootNamespace, $entitiesFolderName, $traitSubDirectories);
        $owningTraitFqn      .= $ownedClassName . '\\Traits\\Has' . $ownedHasName
            . '\\Has' . $ownedHasName . $this->stripPrefixFromHasType($hasType);

        return $owningTraitFqn;
    }

    public function getOwningRelationsRootFqn(
        string $projectRootNamespace,
        string $entitiesFolderName,
        array $subDirectories
    ): string
    {
        $relationsRootFqn = $projectRootNamespace
            . '\\' . $entitiesFolderName
            . '\\Relations\\';
        if (count($subDirectories)) {
            $relationsRootFqn .= implode('\\', $subDirectories) . '\\';
        }
        return $relationsRootFqn;
    }

    public function getOwningInterfaceFqn(
        string $hasType,
        string $ownedEntityFqn,
        string $projectRootNamespace = null,
        string $srcFolder = AbstractCommand::DEFAULT_SRC_SUBFOLDER,
        string $entitiesFolderName = AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER
    ): string
    {
        $ownedHasName = $this->getOwnedHasName($hasType, $ownedEntityFqn);
        if (null === $projectRootNamespace) {
            $projectRootNamespace = $this->getProjectRootNamespaceFromComposerJson($srcFolder);
        }
        list($ownedClassName, , $ownedSubDirectories) = $this->parseFullyQualifiedName(
            $ownedEntityFqn,
            $srcFolder,
            $projectRootNamespace
        );
        $interfaceSubDirectories = array_slice($ownedSubDirectories, 2);
        $owningInterfaceFqn      = $this->getOwningRelationsRootFqn($projectRootNamespace, $entitiesFolderName, $interfaceSubDirectories);
        $owningInterfaceFqn      .= '\\' . $ownedClassName . '\\Interfaces\\Has' . $ownedHasName;
        return $owningInterfaceFqn;
    }

    /**
     * Inverse hasTypes use the standard template without the prefix
     * The exclusion ot this are the ManyToMany and OneToOne relations
     *
     * @param string $hasType
     *
     * @return string
     */
    public function stripPrefixFromHasType(string $hasType): string
    {
        foreach ([RelationsGenerator::INTERNAL_TYPE_MANY_TO_MANY, RelationsGenerator::INTERNAL_TYPE_ONE_TO_ONE] as $noStrip) {
            if (false !== strpos($hasType, $noStrip)) {
                return $hasType;
            }
        }

        foreach ([RelationsGenerator::INTERNAL_TYPE_ONE_TO_MANY, RelationsGenerator::INTERNAL_TYPE_MANY_TO_ONE] as $stripAll) {
            if (false !== strpos($hasType, $stripAll)) {
                return str_replace(
                    [
                        RelationsGenerator::PREFIX_OWNING,
                        RelationsGenerator::PREFIX_INVERSE
                    ],
                    '',
                    $hasType
                );
            }
        }

        return str_replace(
            [
                RelationsGenerator::PREFIX_INVERSE
            ],
            '',
            $hasType
        );
    }

}

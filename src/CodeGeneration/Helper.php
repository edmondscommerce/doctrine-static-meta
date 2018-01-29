<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class Helper
{
    public function calculateEntityNamespaceRootFromTwoEntities(string $entity1Fqn, string $entity2Fqn): string
    {
        $entity1parts = array_flip(explode('\\', $entity1Fqn));
        $entity2parts = array_flip(explode('\\', $entity2Fqn));
        $intersect    = [];
        foreach ($entity1parts as $part => $i) {
            if (isset($entity2parts[$part])) {
                $intersect[] = $part;
            }
        }
        return implode('\\', $intersect);
    }

    public function calculateProjectNamespaceRootFromTwoEntities(string $entity1Fqn, string $entity2Fqn): string
    {
        $entityRootNamespace = $this->calculateEntityNamespaceRootFromTwoEntities($entity1Fqn, $entity2Fqn);
        return substr($entityRootNamespace, 0, strrpos($entityRootNamespace, '\\'));
    }

    public function calculateOwnedHasName(string $hasType, string $ownedEntityFqn): string
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
     */
    public function parseFullyQualifiedName(string $fqn, string $srcOrTestSubFolder, string $projectRootNamespace): array
    {
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

    public function getEntitySubNamespace(
        string $entityFqn,
        string $entitiesRootNamespace
    ): string
    {
        $entitySubFqn = substr($entityFqn, strlen($entitiesRootNamespace)+1);
        $entitySubFqn = substr($entitySubFqn, 0, strrpos($entitySubFqn, '\\'));
        return $entitySubFqn;
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
}

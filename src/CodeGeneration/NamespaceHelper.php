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
    public function swapSuffix(string $fqn, string $currentSuffix, string $newSuffix): string
    {
        return $this->cropSuffix($fqn, $currentSuffix) . $newSuffix;
    }

    /**
     * Crop a suffix from an FQN if it is there.
     *
     * If it is not there, do nothing and return the FQN as is
     *
     * @param string $fqn
     * @param string $suffix
     *
     * @return string
     */
    public function cropSuffix(string $fqn, string $suffix): string
    {
        if ($suffix === \substr($fqn, -\strlen($suffix))) {
            return \substr($fqn, 0, -\strlen($suffix));
        }

        return $fqn;
    }

    /**
     * @param mixed|object $object
     *
     * @return string
     */
    public function getObjectShortName($object): string
    {
        return $this->getClassShortName($this->getObjectFqn($object));
    }

    /**
     * @param string $className
     *
     * @return string
     */
    public function getClassShortName(string $className): string
    {
        $exp = explode('\\', $className);

        return end($exp);
    }

    /**
     * @param mixed|object $object
     *
     * @see https://gist.github.com/ludofleury/1708784
     * @return string
     */
    public function getObjectFqn($object): string
    {
        return \get_class($object);
    }

    /**
     * Get the basename of a namespace
     *
     * @param string $namespace
     *
     * @return string
     */
    public function basename(string $namespace): string
    {
        $strrpos = \strrpos($namespace, '\\');
        if (false === $strrpos) {
            return $namespace;
        }

        return $this->tidy(\substr($namespace, $strrpos + 1));
    }

    /**
     * Checks and tidies up a given namespace
     *
     * @param string $namespace
     *
     * @return string
     * @throws \RuntimeException
     */
    public function tidy(string $namespace): string
    {
        if (\ts\stringContains($namespace, '/')) {
            throw new \RuntimeException('Invalid namespace ' . $namespace);
        }
        #remove repeated separators
        $namespace = preg_replace(
            '#' . '\\\\' . '+#',
            '\\',
            $namespace
        );

        return $namespace;
    }

    /**
     * Get the fully qualified name of the Fixture class for a specified Entity fully qualified name
     *
     * @param string $entityFqn
     *
     * @return string
     */
    public function getFixtureFqnFromEntityFqn(string $entityFqn): string
    {
        return \str_replace(
                   '\\Entities',
                   '\\Assets\\Entity\\Fixtures',
                   $entityFqn
               ) . 'Fixture';
    }

    /**
     * Get the fully qualified name of the Entity for a specified Entity fully qualified name
     *
     * @param string $fixtureFqn
     *
     * @return string
     */
    public function getEntityFqnFromFixtureFqn(string $fixtureFqn): string
    {
        return \substr(
            \str_replace(
                '\\Assets\\Entity\\Fixtures',
                '\\Entities',
                $fixtureFqn
            ),
            0,
            -\strlen('Fixture')
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
        return $this->getEntitySubPath($entityFqn) . '.php';
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

        return '/' . $entityPath;
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
                    '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\'
                )
                + \strlen('\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\')
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
                           . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                           . '\\' . $this->getEntitySubNamespace($entityFqn)
                           . '\\Traits';

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
                    '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\'
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

        return $interfaceNamespace . '\\Has' . ucfirst($entityFqn::getDoctrineStaticMeta()->getPlural()) . 'Interface';
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
                               . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE
                               . '\\' . $this->getEntitySubNamespace($entityFqn)
                               . '\\Interfaces';

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

            return $interfaceNamespace . '\\Has' . ucfirst($entityFqn::getDoctrineStaticMeta()->getSingular())
                   . 'Interface';
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
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
            $ownedHasName = $this->getOwnedHasName($hasType, $ownedEntityFqn, $srcFolder, $projectRootNamespace);
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
            $owningTraitFqn      .= $ownedClassName . '\\Traits\\Has' . $ownedHasName
                                    . '\\Has' . $ownedHasName . $this->stripPrefixFromHasType($hasType);

            return $this->tidy($owningTraitFqn);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Based on the $hasType, we calculate exactly what type of `Has` we have
     *
     * @param string $hasType
     * @param string $ownedEntityFqn
     * @param string $srcOrTestSubFolder
     *
     * @param string $projectRootNamespace
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function getOwnedHasName(
        string $hasType,
        string $ownedEntityFqn,
        string $srcOrTestSubFolder,
        string $projectRootNamespace
    ): string {
        $parsedFqn = $this->parseFullyQualifiedName(
            $ownedEntityFqn,
            $srcOrTestSubFolder,
            $projectRootNamespace
        );

        $subDirectories = $parsedFqn[2];

        if (\in_array(
            $hasType,
            RelationsGenerator::HAS_TYPES_PLURAL,
            true
        )) {
            return $this->getPluralNamespacedName($ownedEntityFqn, $subDirectories);
        }

        return $this->getSingularNamespacedName($ownedEntityFqn, $subDirectories);
    }

    /**
     * From the fully qualified name, parse out:
     *  - class name,
     *  - namespace
     *  - the namespace parts not including the project root namespace
     *
     * @param string      $fqn
     *
     * @param string      $srcOrTestSubFolder eg 'src' or 'test'
     *
     * @param string|null $projectRootNamespace
     *
     * @return array [$className,$namespace,$subDirectories]
     * @throws DoctrineStaticMetaException
     */
    public function parseFullyQualifiedName(
        string $fqn,
        string $srcOrTestSubFolder = AbstractCommand::DEFAULT_SRC_SUBFOLDER,
        string $projectRootNamespace = null
    ): array {
        try {
            $fqn = $this->root($fqn);
            if (null === $projectRootNamespace) {
                $projectRootNamespace = $this->getProjectRootNamespaceFromComposerJson($srcOrTestSubFolder);
            }
            $projectRootNamespace = $this->root($projectRootNamespace);
            if (false === \ts\stringContains($fqn, $projectRootNamespace)) {
                throw new DoctrineStaticMetaException(
                    'The $fqn [' . $fqn . '] does not contain the project root namespace'
                    . ' [' . $projectRootNamespace . '] - are you sure it is the correct FQN?'
                );
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
                $this->root($namespace),
                $subDirectories,
            ];
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Generate a tidy root namespace without a leading \
     *
     * @param string $namespace
     *
     * @return string
     */
    public function root(string $namespace): string
    {
        return $this->tidy(ltrim($namespace, '\\'));
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
            $jsonPath        = Config::getProjectRootDirectory() . '/composer.json';
            $json            = json_decode(\ts\file_get_contents($jsonPath), true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \RuntimeException(
                    'Error decoding json from path ' . $jsonPath . ' , ' . json_last_error_msg()
                );
            }
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
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        throw new DoctrineStaticMetaException('Failed to find psr-4 namespace root');
    }

    /**
     * @param string $entityFqn
     * @param array  $subDirectories
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getPluralNamespacedName(string $entityFqn, array $subDirectories): string
    {
        $plural = \ucfirst(MappingHelper::getPluralForFqn($entityFqn));

        return $this->getNamespacedName($plural, $subDirectories);
    }

    /**
     * @param string $entityName
     * @param array  $subDirectories
     *
     * @return string
     */
    public function getNamespacedName(string $entityName, array $subDirectories): string
    {
        $noEntitiesDirectory = \array_slice($subDirectories, 2);
        $namespacedName      = \array_merge($noEntitiesDirectory, [$entityName]);

        return \ucfirst(\implode('', $namespacedName));
    }

    /**
     * @param string $entityFqn
     * @param array  $subDirectories
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getSingularNamespacedName(string $entityFqn, array $subDirectories): string
    {
        $singular = \ucfirst(MappingHelper::getSingularForFqn($entityFqn));

        return $this->getNamespacedName($singular, $subDirectories);
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
                            . AbstractGenerator::ENTITY_RELATIONS_NAMESPACE . '\\';
        if (count($subDirectories) > 0) {
            $relationsRootFqn .= implode('\\', $subDirectories) . '\\';
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
            if (\ts\stringContains($hasType, $noStrip)) {
                return $hasType;
            }
        }

        foreach ([
                     RelationsGenerator::INTERNAL_TYPE_ONE_TO_MANY,
                     RelationsGenerator::INTERNAL_TYPE_MANY_TO_ONE,
                 ] as $stripAll) {
            if (\ts\stringContains($hasType, $stripAll)) {
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

    public function getFactoryFqnFromEntityFqn(string $entityFqn): string
    {
        return $this->tidy(
            \str_replace(
                '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
                '\\' . AbstractGenerator::ENTITY_FACTORIES_NAMESPACE . '\\',
                $entityFqn
            ) . 'Factory'
        );
    }

    public function getDtoFactoryFqnFromEntityFqn(string $entityFqn): string
    {
        return $this->tidy(
            \str_replace(
                '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
                '\\' . AbstractGenerator::ENTITY_FACTORIES_NAMESPACE . '\\',
                $entityFqn
            ) . 'DtoFactory'
        );
    }

    public function getRepositoryqnFromEntityFqn(string $entityFqn): string
    {
        return $this->tidy(
            \str_replace(
                '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
                '\\' . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE . '\\',
                $entityFqn
            ) . 'Repository'
        );
    }

    /**
     * @param string $ownedEntityFqn
     * @param string $srcOrTestSubFolder
     * @param string $projectRootNamespace
     *
     * @return string
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function getReciprocatedHasName(
        string $ownedEntityFqn,
        string $srcOrTestSubFolder,
        string $projectRootNamespace
    ): string {
        $parsedFqn = $this->parseFullyQualifiedName(
            $ownedEntityFqn,
            $srcOrTestSubFolder,
            $projectRootNamespace
        );

        $subDirectories = $parsedFqn[2];

        return $this->getSingularNamespacedName($ownedEntityFqn, $subDirectories);
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
            $ownedHasName = $this->getOwnedHasName($hasType, $ownedEntityFqn, $srcFolder, $projectRootNamespace);
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
            $owningInterfaceFqn      .= '\\' . $ownedClassName . '\\Interfaces\\Has' . $ownedHasName . 'Interface';

            return $this->tidy($owningInterfaceFqn);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getEntityInterfaceFromEntityFqn(string $entityFqn): string
    {
        return \str_replace(
                   '\\Entities\\',
                   '\\Entity\\Interfaces\\',
                   $entityFqn
               ) . 'Interface';
    }

    public function getEntityFqnFromEntityInterfaceFqn(string $entityInterfaceFqn): string
    {
        return substr(
            \str_replace(
                '\\Entity\\Interfaces\\',
                '\\Entities\\',
                $entityInterfaceFqn
            ),
            0,
            -\strlen('Interface')
        );
    }

    public function getEntityFqnFromEntityFactoryFqn(string $entityFactoryFqn): string
    {
        return substr(
            \str_replace(
                '\\Entity\\Factories\\',
                '\\Entities\\',
                $entityFactoryFqn
            ),
            0,
            -\strlen('Factory')
        );
    }

    public function getEntityFqnFromEntityDtoFactoryFqn(string $entityDtoFactoryFqn): string
    {
        return substr(
            \str_replace(
                '\\Entity\\Factories\\',
                '\\Entities\\',
                $entityDtoFactoryFqn
            ),
            0,
            -\strlen('DtoFactory')
        );
    }

    public function getEntityDtoFqnFromEntityFqn(string $entityFqn): string
    {
        return \str_replace(
                   '\\Entities\\',
                   '\\Entity\\DataTransferObjects\\',
                   $entityFqn
               ) . 'Dto';
    }

    public function getEntityFqnFromEntityDtoFqn(string $entityDtoFqn): string
    {
        return substr(
            \str_replace(
                '\\Entity\\DataTransferObjects\\',
                '\\Entities\\',
                $entityDtoFqn
            ),
            0,
            -\strlen('Dto')
        );
    }

    public function getEntityFqnFromEntityRepositoryFqn(string $entityRepositoryFqn): string
    {
        return substr(
            \str_replace(
                '\\Entity\\Repositories\\',
                '\\Entities\\',
                $entityRepositoryFqn
            ),
            0,
            -\strlen('Repository')
        );
    }

    public function getEntityFqnFromEntitySaverFqn(string $entitySaverFqn): string
    {
        return substr(
            \str_replace(
                '\\Entity\\Savers\\',
                '\\Entities\\',
                $entitySaverFqn
            ),
            0,
            -\strlen('Saver')
        );
    }

    public function getEntitySaverFqnFromEntityFqn(string $entityFqn): string
    {
        return \str_replace(
                   '\\Entities\\',
                   '\\Entity\\Savers\\',
                   $entityFqn
               ) . 'Saver';
    }

    public function getEntityFqnFromEntityTestFqn(string $entityTestFqn): string
    {
        return \substr(
            $entityTestFqn,
            0,
            -\strlen('Test')
        );
    }

    public function getEntityTestFqnFromEntityFqn(string $entityFqn): string
    {
        return $entityFqn . 'Test';
    }

    public function getFqnFromPath(string $path, string $namespaceRoot): string
    {
        preg_match('%/(src|tests)/(.+?)\.php%', $path, $matches);

        return $namespaceRoot . '\\' . str_replace('/', '\\', $matches[2]);
    }
}

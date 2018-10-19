<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Relations\GenerateRelationCodeForEntity;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;
use PhpParser\Error;

/**
 * Class RelationsGenerator
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RelationsGenerator extends AbstractGenerator
{
    public const PREFIX_OWNING         = 'Owning';
    public const PREFIX_INVERSE        = 'Inverse';
    public const PREFIX_UNIDIRECTIONAL = 'Unidirectional';
    public const PREFIX_REQUIRED       = 'Required';


    /*******************************************************************************************************************
     * OneToOne - One instance of the current Entity refers to One instance of the referred Entity.
     */
    public const INTERNAL_TYPE_ONE_TO_ONE = 'OneToOne';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityOwningOneToOne.php
     */
    public const HAS_ONE_TO_ONE = self::PREFIX_OWNING . self::INTERNAL_TYPE_ONE_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntity/HasRequiredTemplateEntityOwningOneToOne.php
     */
    public const HAS_REQUIRED_ONE_TO_ONE = self::PREFIX_REQUIRED . self::PREFIX_OWNING . self::INTERNAL_TYPE_ONE_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityInverseOneToOne.php
     */
    public const HAS_INVERSE_ONE_TO_ONE = self::PREFIX_INVERSE . self::INTERNAL_TYPE_ONE_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntity/HasRequiredTemplateEntityInverseOneToOne.php
     */
    public const HAS_REQUIRED_INVERSE_ONE_TO_ONE = self::PREFIX_REQUIRED .
                                                   self::PREFIX_INVERSE .
                                                   self::INTERNAL_TYPE_ONE_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityUnidrectionalOneToOne.php
     */
    public const HAS_UNIDIRECTIONAL_ONE_TO_ONE = self::PREFIX_UNIDIRECTIONAL . self::INTERNAL_TYPE_ONE_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntity/HasRequiredTemplateEntityUnidrectionalOneToOne.php
     */
    public const HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_ONE = self::PREFIX_REQUIRED .
                                                          self::PREFIX_UNIDIRECTIONAL .
                                                          self::INTERNAL_TYPE_ONE_TO_ONE;

    /*******************************************************************************************************************
     * OneToMany - One instance of the current Entity has Many instances (references) to the referred Entity.
     */
    public const INTERNAL_TYPE_ONE_TO_MANY = 'OneToMany';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    public const HAS_ONE_TO_MANY = self::INTERNAL_TYPE_ONE_TO_MANY;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntities/HasRequiredTemplateEntitiesOneToMany.php
     */
    public const HAS_REQUIRED_ONE_TO_MANY = self::PREFIX_REQUIRED . self::INTERNAL_TYPE_ONE_TO_MANY;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    public const HAS_UNIDIRECTIONAL_ONE_TO_MANY = self::PREFIX_UNIDIRECTIONAL . self::INTERNAL_TYPE_ONE_TO_MANY;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntities/HasRequiredTemplateEntitiesOneToMany.php
     */
    public const HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_MANY = self::PREFIX_REQUIRED .
                                                           self::PREFIX_UNIDIRECTIONAL .
                                                           self::INTERNAL_TYPE_ONE_TO_MANY;

    /*******************************************************************************************************************
     * ManyToOne - Many instances of the current Entity refer to One instance of the referred Entity.
     */
    public const INTERNAL_TYPE_MANY_TO_ONE = 'ManyToOne';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    public const HAS_MANY_TO_ONE = self::INTERNAL_TYPE_MANY_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntity/HasRequiredTemplateEntityManyToOne.php
     */
    public const HAS_REQUIRED_MANY_TO_ONE = self::PREFIX_REQUIRED . self::INTERNAL_TYPE_MANY_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    public const HAS_UNIDIRECTIONAL_MANY_TO_ONE = self::PREFIX_UNIDIRECTIONAL . self::INTERNAL_TYPE_MANY_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntity/HasRequiredTemplateEntityManyToOne.php
     */
    public const HAS_REQUIRED_UNIDIRECTIONAL_MANY_TO_ONE = self::PREFIX_REQUIRED .
                                                           self::PREFIX_UNIDIRECTIONAL .
                                                           self::INTERNAL_TYPE_MANY_TO_ONE;


    /*******************************************************************************************************************
     * ManyToMany - Many instances of the current Entity refer to Many instance of the referred Entity.
     */
    public const INTERNAL_TYPE_MANY_TO_MANY = 'ManyToMany';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOwningManyToMany.php
     */
    public const HAS_MANY_TO_MANY = self::PREFIX_OWNING . self::INTERNAL_TYPE_MANY_TO_MANY;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntities/HasRequiredTemplateEntitiesOwningManyToMany.php
     */
    public const HAS_REQUIRED_MANY_TO_MANY = self::PREFIX_REQUIRED .
                                             self::PREFIX_OWNING .
                                             self::INTERNAL_TYPE_MANY_TO_MANY;
    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesInverseManyToMany.php
     */
    public const HAS_INVERSE_MANY_TO_MANY = self::PREFIX_INVERSE . self::INTERNAL_TYPE_MANY_TO_MANY;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasRequiredTemplateEntities/HasRequiredTemplateEntitiesInverseManyToMany.php
     */
    public const HAS_REQUIRED_INVERSE_MANY_TO_MANY = self::PREFIX_REQUIRED .
                                                     self::PREFIX_INVERSE .
                                                     self::INTERNAL_TYPE_MANY_TO_MANY;


    /**
     * The full list of possible relation types
     */
    public const HAS_TYPES = [
        self::HAS_ONE_TO_ONE,
        self::HAS_INVERSE_ONE_TO_ONE,
        self::HAS_UNIDIRECTIONAL_ONE_TO_ONE,
        self::HAS_ONE_TO_MANY,
        self::HAS_UNIDIRECTIONAL_ONE_TO_MANY,
        self::HAS_MANY_TO_ONE,
        self::HAS_UNIDIRECTIONAL_MANY_TO_ONE,
        self::HAS_MANY_TO_MANY,
        self::HAS_INVERSE_MANY_TO_MANY,

        self::HAS_REQUIRED_ONE_TO_ONE,
        self::HAS_REQUIRED_INVERSE_ONE_TO_ONE,
        self::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_ONE,
        self::HAS_REQUIRED_ONE_TO_MANY,
        self::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_MANY,
        self::HAS_REQUIRED_MANY_TO_ONE,
        self::HAS_REQUIRED_UNIDIRECTIONAL_MANY_TO_ONE,
        self::HAS_REQUIRED_MANY_TO_MANY,
        self::HAS_REQUIRED_INVERSE_MANY_TO_MANY,
    ];

    /**
     * Of the full list, which ones will be automatically reciprocated in the generated code
     */
    public const HAS_TYPES_RECIPROCATED = [
        self::HAS_ONE_TO_ONE,
        self::HAS_INVERSE_ONE_TO_ONE,
        self::HAS_ONE_TO_MANY,
        self::HAS_MANY_TO_ONE,
        self::HAS_MANY_TO_MANY,
        self::HAS_INVERSE_MANY_TO_MANY,

        self::HAS_REQUIRED_ONE_TO_ONE,
        self::HAS_REQUIRED_INVERSE_ONE_TO_ONE,
        self::HAS_REQUIRED_ONE_TO_MANY,
        self::HAS_REQUIRED_MANY_TO_ONE,
        self::HAS_REQUIRED_MANY_TO_MANY,
        self::HAS_REQUIRED_INVERSE_MANY_TO_MANY,
    ];

    /**
     *Of the full list, which ones are unidirectional (i.e not reciprocated)
     */
    public const HAS_TYPES_UNIDIRECTIONAL = [
        self::HAS_UNIDIRECTIONAL_MANY_TO_ONE,
        self::HAS_UNIDIRECTIONAL_ONE_TO_MANY,
        self::HAS_UNIDIRECTIONAL_ONE_TO_ONE,

        self::HAS_REQUIRED_UNIDIRECTIONAL_MANY_TO_ONE,
        self::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_MANY,
        self::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_ONE,
    ];

    /**
     * Of the full list, which ones are a plural relationship, i.e they have multiple of the related entity
     */
    public const HAS_TYPES_PLURAL = [
        self::HAS_MANY_TO_MANY,
        self::HAS_INVERSE_MANY_TO_MANY,
        self::HAS_ONE_TO_MANY,
        self::HAS_UNIDIRECTIONAL_ONE_TO_MANY,

        self::HAS_REQUIRED_MANY_TO_MANY,
        self::HAS_REQUIRED_INVERSE_MANY_TO_MANY,
        self::HAS_REQUIRED_ONE_TO_MANY,
        self::HAS_REQUIRED_UNIDIRECTIONAL_ONE_TO_MANY,
    ];

    /**
     * Set a relationship from one Entity to Another Entity.
     *
     * Also used internally to set the reciprocal side. Uses an undocumented 4th bool parameter to kill recursion.
     *
     * @param string $owningEntityFqn
     * @param string $hasType
     * @param string $ownedEntityFqn
     * @param bool   $requiredReciprocation
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setEntityHasRelationToEntity(
        string $owningEntityFqn,
        string $hasType,
        string $ownedEntityFqn,
        bool $requiredReciprocation = false
    ): void {
        $reciprocate = (false === isset(\func_get_args()[4]));
        try {
            $this->validateHasType($hasType);
            list(
                $owningTraitPath,
                $owningInterfacePath,
                $reciprocatingInterfacePath,
                ) = $this->getPathsForOwningTraitsAndInterfaces(
                $hasType,
                $ownedEntityFqn
            );
            list($owningClass, , $owningClassSubDirs) = $this->parseFullyQualifiedName($owningEntityFqn);
            $owningClassPath = $this->pathHelper->getPathFromNameAndSubDirs(
                $this->pathToProjectRoot,
                $owningClass,
                $owningClassSubDirs
            );
            $this->useRelationTraitInClass($owningClassPath, $owningTraitPath);
            $this->useRelationInterfaceInEntityInterface($owningClassPath, $owningInterfacePath);
            if (true === $reciprocate && \in_array($hasType, self::HAS_TYPES_RECIPROCATED, true)) {
                $this->useRelationInterfaceInEntityInterface($owningClassPath, $reciprocatingInterfacePath);
                $inverseType = $this->getInverseHasType($hasType);
                $inverseType = $this->updateHasTypeForPossibleRequired($inverseType, $requiredReciprocation);
                $this->setEntityHasRelationToEntity(
                    $ownedEntityFqn,
                    $inverseType,
                    $owningEntityFqn,
                    $requiredReciprocation,
                    /**
                     * extra unspecified argument to kill recursive reciprocation
                     */
                    false
                );
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $hasType
     *
     * @throws \InvalidArgumentException
     */
    protected function validateHasType(string $hasType): void
    {
        if (!\in_array($hasType, static::HAS_TYPES, true)) {
            throw new \InvalidArgumentException(
                'Invalid $hasType ' . $hasType . ', must be one of: '
                . \print_r(static::HAS_TYPES, true)
            );
        }
    }

    /**
     * Get the absolute paths for the owning traits and interfaces for the specified relation type
     * Will ensure that the files exists
     *
     * @param string $hasType
     * @param string $ownedEntityFqn
     *
     * @return array [
     *  $owningTraitPath,
     *  $owningInterfacePath,
     *  $reciprocatingInterfacePath
     * ]
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function getPathsForOwningTraitsAndInterfaces(
        string $hasType,
        string $ownedEntityFqn
    ): array {
        try {
            $ownedHasName        = $this->namespaceHelper->getOwnedHasName(
                $hasType,
                $ownedEntityFqn,
                $this->srcSubFolderName,
                $this->projectRootNamespace
            );
            $reciprocatedHasName = $this->namespaceHelper->getReciprocatedHasName(
                $ownedEntityFqn,
                $this->srcSubFolderName,
                $this->projectRootNamespace
            );
            $owningTraitFqn      = $this->getOwningTraitFqn($hasType, $ownedEntityFqn);
            list($traitName, , $traitSubDirsNoEntities) = $this->parseFullyQualifiedName($owningTraitFqn);
            $owningTraitPath = $this->pathHelper->getPathFromNameAndSubDirs(
                $this->pathToProjectRoot,
                $traitName,
                $traitSubDirsNoEntities
            );
            if (!\file_exists($owningTraitPath)) {
                $this->generateRelationCodeForEntity($ownedEntityFqn);
            }
            $owningInterfaceFqn = $this->getOwningInterfaceFqn($hasType, $ownedEntityFqn);
            list($interfaceName, , $interfaceSubDirsNoEntities) = $this->parseFullyQualifiedName($owningInterfaceFqn);
            $owningInterfacePath        = $this->pathHelper->getPathFromNameAndSubDirs(
                $this->pathToProjectRoot,
                $interfaceName,
                $interfaceSubDirsNoEntities
            );
            $reciprocatingInterfacePath = \str_replace(
                'Has' . $ownedHasName,
                'Reciprocates' . $reciprocatedHasName,
                $owningInterfacePath
            );

            return [
                $owningTraitPath,
                $owningInterfacePath,
                $reciprocatingInterfacePath,
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
     * @param string $hasType
     * @param string $ownedEntityFqn
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getOwningTraitFqn(string $hasType, string $ownedEntityFqn): string
    {
        return $this->namespaceHelper->getOwningTraitFqn(
            $hasType,
            $ownedEntityFqn,
            $this->projectRootNamespace,
            $this->srcSubFolderName
        );
    }

    /**
     * Generate the relation traits for specified Entity
     *
     * This works by copying the template traits folder over and then updating the file contents, name and path
     *
     * @param string $entityFqn Fully Qualified Name of Entity
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function generateRelationCodeForEntity(string $entityFqn): void
    {
        $invokable = new GenerateRelationCodeForEntity(
            $entityFqn,
            $this->pathToProjectRoot,
            $this->projectRootNamespace,
            $this->srcSubFolderName,
            $this->namespaceHelper,
            $this->pathHelper,
            $this->findAndReplaceHelper
        );
        $invokable($this->getRelativePathRelationsGenerator());
    }

    /**
     * Generator that yields relative paths of all the files in the relations template path and the SplFileInfo objects
     *
     * Use a PHP Generator to iterate over a recursive iterator iterator and then yield:
     * - key: string $relativePath
     * - value: \SplFileInfo $fileInfo
     *
     * The `finally` step unsets the recursiveIterator once everything is done
     *
     * @return \Generator
     */
    public function getRelativePathRelationsGenerator(): \Generator
    {
        try {
            $recursiveIterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    \realpath(AbstractGenerator::RELATIONS_TEMPLATE_PATH),
                    \RecursiveDirectoryIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($recursiveIterator as $path => $fileInfo) {
                $relativePath = rtrim(
                    $this->getFilesystem()->makePathRelative(
                        $path,
                        \realpath(AbstractGenerator::RELATIONS_TEMPLATE_PATH)
                    ),
                    '/'
                );
                yield $relativePath => $fileInfo;
            }
        } finally {
            $recursiveIterator = null;
            unset($recursiveIterator);
        }
    }

    /**
     * @param string $hasType
     * @param string $ownedEntityFqn
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    public function getOwningInterfaceFqn(string $hasType, string $ownedEntityFqn): string
    {
        return $this->namespaceHelper->getOwningInterfaceFqn(
            $hasType,
            $ownedEntityFqn,
            $this->projectRootNamespace,
            $this->srcSubFolderName
        );
    }

    /**
     * Add the specified trait to the specified class
     *
     * @param string $classPath
     * @param string $traitPath
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function useRelationTraitInClass(string $classPath, string $traitPath): void
    {
        try {
            $class = PhpClass::fromFile($classPath);
        } catch (Error $e) {
            throw new DoctrineStaticMetaException(
                'PHP parsing error when loading class ' . $classPath . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        try {
            $trait = PhpTrait::fromFile($traitPath);
        } catch (Error $e) {
            throw new DoctrineStaticMetaException(
                'PHP parsing error when loading class ' . $classPath . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        $class->addTrait($trait);
        $this->codeHelper->generate($class, $classPath);
    }

    /**
     * Add the specified interface to the specified entity interface
     *
     * @param string $classPath
     * @param string $interfacePath
     *
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function useRelationInterfaceInEntityInterface(string $classPath, string $interfacePath): void
    {
        $entityFqn           = PhpClass::fromFile($classPath)->getQualifiedName();
        $entityInterfaceFqn  = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
        $entityInterfacePath = (new \ts\Reflection\ReflectionClass($entityInterfaceFqn))->getFileName();
        $entityInterface     = PhpInterface::fromFile($entityInterfacePath);
        $relationInterface   = PhpInterface::fromFile($interfacePath);
        $entityInterface->addInterface($relationInterface);
        $this->codeHelper->generate($entityInterface, $entityInterfacePath);
    }

    /**
     * Get the inverse of a hasType
     *
     * @param string $hasType
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    protected function getInverseHasType(string $hasType): string
    {
        switch ($hasType) {
            case self::HAS_ONE_TO_ONE:
            case self::HAS_REQUIRED_ONE_TO_ONE:
            case self::HAS_MANY_TO_MANY:
            case self::HAS_REQUIRED_MANY_TO_MANY:
                return \str_replace(
                    self::PREFIX_OWNING,
                    self::PREFIX_INVERSE,
                    $hasType
                );

            case self::HAS_INVERSE_ONE_TO_ONE:
            case self::HAS_REQUIRED_INVERSE_ONE_TO_ONE:
            case self::HAS_INVERSE_MANY_TO_MANY:
            case self::HAS_REQUIRED_INVERSE_MANY_TO_MANY:
                return \str_replace(
                    self::PREFIX_INVERSE,
                    self::PREFIX_OWNING,
                    $hasType
                );

            case self::HAS_MANY_TO_ONE:
                return self::HAS_ONE_TO_MANY;

            case self::HAS_REQUIRED_MANY_TO_ONE:
                return self::HAS_REQUIRED_ONE_TO_MANY;

            case self::HAS_ONE_TO_MANY:
                return self::HAS_MANY_TO_ONE;

            case self::HAS_REQUIRED_ONE_TO_MANY:
                return self::HAS_REQUIRED_MANY_TO_ONE;

            default:
                throw new DoctrineStaticMetaException(
                    'invalid $hasType ' . $hasType . ' when trying to get the inverted relation'
                );
        }

    }

    /**
     * Take a relationship and a possibility of being required and ensure it is set as the correct relationship
     *
     * @param string $relation
     * @param bool   $required
     *
     * @return string
     */
    private function updateHasTypeForPossibleRequired(string $relation, bool $required): string
    {
        $inverseIsRequired = \ts\stringContains($relation, self::PREFIX_REQUIRED);
        if (false === $required) {
            if (false === $inverseIsRequired) {
                return $relation;
            }

            return $this->removeRequiredToRelation($relation);

        }
        if (true === $required) {
            if (true === $inverseIsRequired) {
                return $relation;
            }

            return $this->addRequiredToRelation($relation);
        }
    }

    private function removeRequiredToRelation(string $relation): string
    {
        return \str_replace('Has' . self::PREFIX_REQUIRED, 'Has', $relation);
    }

    private function addRequiredToRelation(string $relation): string
    {
        return \str_replace('Has', 'Has' . self::PREFIX_REQUIRED, $relation);
    }
}

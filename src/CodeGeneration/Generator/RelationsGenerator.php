<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;

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


    /*******************************************************************************************************************
     * OneToOne - One instance of the current Entity refers to One instance of the referred Entity.
     */
    public const INTERNAL_TYPE_ONE_TO_ONE = 'OneToOne';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityOwningOneToOne.php
     */
    public const HAS_ONE_TO_ONE = self::PREFIX_OWNING.self::INTERNAL_TYPE_ONE_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityInverseOneToOne.php
     */
    public const HAS_INVERSE_ONE_TO_ONE = self::PREFIX_INVERSE.self::INTERNAL_TYPE_ONE_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityUnidrectionalOneToOne.php
     */
    public const HAS_UNIDIRECTIONAL_ONE_TO_ONE = self::PREFIX_UNIDIRECTIONAL.self::INTERNAL_TYPE_ONE_TO_ONE;


    /*******************************************************************************************************************
     * OneToMany - One instance of the current Entity has Many instances (references) to the referred Entity.
     */
    public const INTERNAL_TYPE_ONE_TO_MANY = 'OneToMany';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    public const HAS_ONE_TO_MANY = self::INTERNAL_TYPE_ONE_TO_MANY;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    public const HAS_UNIDIRECTIONAL_ONE_TO_MANY = self::PREFIX_UNIDIRECTIONAL.self::INTERNAL_TYPE_ONE_TO_MANY;


    /*******************************************************************************************************************
     * ManyToOne - Many instances of the current Entity refer to One instance of the referred Entity.
     */
    public const INTERNAL_TYPE_MANY_TO_ONE = 'ManyToOne';
    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    public const HAS_MANY_TO_ONE = self::INTERNAL_TYPE_MANY_TO_ONE;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    public const HAS_UNIDIRECTIONAL_MANY_TO_ONE = self::PREFIX_UNIDIRECTIONAL.self::INTERNAL_TYPE_MANY_TO_ONE;


    /*******************************************************************************************************************
     * ManyToMany - Many instances of the current Entity refer to Many instance of the referred Entity.
     */
    public const INTERNAL_TYPE_MANY_TO_MANY = 'ManyToMany';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOwningManyToMany.php
     */
    public const HAS_MANY_TO_MANY = self::PREFIX_OWNING.self::INTERNAL_TYPE_MANY_TO_MANY;

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesInverseManyToMany.php
     */
    public const HAS_INVERSE_MANY_TO_MANY = self::PREFIX_INVERSE.self::INTERNAL_TYPE_MANY_TO_MANY;


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
    ];

    /**
     * Of the full list, which ones are a plural relationship, i.e they have multiple of the related entity
     */
    public const HAS_TYPES_PLURAL = [
        self::HAS_MANY_TO_MANY,
        self::HAS_INVERSE_MANY_TO_MANY,
        self::HAS_ONE_TO_MANY,
        self::HAS_UNIDIRECTIONAL_ONE_TO_MANY,
    ];

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
                    $this->getFilesystem()->makePathRelative($path, AbstractGenerator::RELATIONS_TEMPLATE_PATH),
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
     * Generate the relation traits for specified Entity
     *
     * This works by copying the template traits folder over and then updating the file contents, name and path
     *
     * @param string $entityFqn Fully Qualified Name of Entity
     *
     * @throws DoctrineStaticMetaException
     */
    public function generateRelationCodeForEntity(string $entityFqn)
    {
        try {
            list($className, , $subDirsNoEntities) = $this->parseFullyQualifiedName($entityFqn);
            $subDirsNoEntities    = \array_slice($subDirsNoEntities, 2);
            $destinationDirectory = $this->pathToProjectSrcRoot
                                    .'/'.$this->srcSubFolderName
                                    .'/'.$this->entitiesFolderName
                                    .'/Relations/'.\implode(
                                        '/',
                                        $subDirsNoEntities
                                    )
                                    .'/'.$className;

            $this->copyTemplateDirectoryAndGetPath(
                AbstractGenerator::RELATIONS_TEMPLATE_PATH,
                $destinationDirectory
            );

            $plural            = \ucfirst($entityFqn::getPlural());
            $singular          = \ucfirst($entityFqn::getSingular());
            $nsNoEntities      = \implode('\\', $subDirsNoEntities);
            $singularWithNs    = \ltrim(
                $nsNoEntities.'\\'.$singular,
                '\\'
            );
            $pluralWithNs      = \ltrim(
                $nsNoEntities.'\\'.$plural,
                '\\'
            );
            $entitiesNamespace = $this->projectRootNamespace.'\\'.$this->entitiesFolderName;
            $dirsToRename      = [];
            $filesCreated      = [];
            //update file contents apart from namespace
            foreach ($this->getRelativePathRelationsGenerator() as $path => $fileInfo) {
                $realPath = \realpath("$destinationDirectory/$path");
                if (false === $realPath) {
                    throw new \RuntimeException("path $destinationDirectory/$path does not exist");
                }
                $path = $realPath;
                if (!$fileInfo->isDir()) {
                    $this->findReplace(
                        'use '.self::FIND_NAMESPACE.'\\'.self::FIND_ENTITY_NAME.';',
                        "use $entityFqn;",
                        $path
                    );
                    $this->findReplaceRegex(
                        '%use(.+?)Relations\\\TemplateEntity(.+?);%',
                        'use ${1}Relations\\'.$singularWithNs.'${2};',
                        $path
                    );
                    $this->findReplaceRegex(
                        '%use(.+?)Relations\\\TemplateEntity(.+?);%',
                        'use ${1}Relations\\'.$pluralWithNs.'${2};',
                        $path
                    );

                    $this->replaceEntityName($singular, $path);
                    $this->replacePluralEntityName($plural, $path);
                    $this->replaceNamespace($entitiesNamespace, $path);
                    $filesCreated[] = $this->renamePathBasenameSingularOrPlural($path, $singular, $plural);
                    continue;
                }
                $dirsToRename[] = $path;
            }
            //update directory names and update file created paths accordingly
            foreach ($dirsToRename as $dirPath) {
                $updateDairPath = $this->renamePathBasenameSingularOrPlural($dirPath, $singular, $plural);
                foreach ($filesCreated as $k => $filePath) {
                    $filesCreated[$k] = \str_replace($dirPath, $updateDairPath, $filePath);
                }
            }
            //now path is totally sorted, update namespace based on path
            foreach ($filesCreated as $filePath) {
                $this->setNamespaceFromPath($filePath);
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception generating relation for entity '.$entityFqn,
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Add the specified interface to the specified class
     *
     * @param string $classPath
     * @param string $interfacePath
     */
    protected function useRelationInterfaceInClass(string $classPath, string $interfacePath)
    {
        $generator = new CodeFileGenerator(
            [
                'generateDocblock'   => false,
                'declareStrictTypes' => true,
            ]
        );
        $class     = PhpClass::fromFile($classPath);
        $interface = PhpInterface::fromFile($interfacePath);
        $class->addInterface($interface);
        $generatedClass = $generator->generate($class);
        \file_put_contents($classPath, $generatedClass);
    }

    /**
     * Add the specified trait to the specified class
     *
     * @param string $classPath
     * @param string $traitPath
     */
    protected function useRelationTraitInClass(string $classPath, string $traitPath)
    {
        $generator = new CodeFileGenerator(
            [
                'generateDocblock'   => false,
                'declareStrictTypes' => true,
            ]
        );
        $class     = PhpClass::fromFile($classPath);
        $trait     = PhpTrait::fromFile($traitPath);
        $class->addTrait($trait);
        $generatedClass = $generator->generate($class);
        \file_put_contents($classPath, $generatedClass);
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
     */
    protected function getPathsForOwningTraitsAndInterfaces(string $hasType, string $ownedEntityFqn): array
    {
        try {
            $ownedHasName        = $this->namespaceHelper->getOwnedHasName($hasType, $ownedEntityFqn);
            $reciprocatedHasName = \ucfirst(MappingHelper::getSingularForFqn($ownedEntityFqn));
            $owningTraitFqn      = $this->getOwningTraitFqn($hasType, $ownedEntityFqn);
            list($traitName, , $traitSubDirsNoEntities) = $this->parseFullyQualifiedName($owningTraitFqn);
            $owningTraitPath = $this->getPathFromNameAndSubDirs($traitName, $traitSubDirsNoEntities);
            if (!\file_exists($owningTraitPath)) {
                $this->generateRelationCodeForEntity($ownedEntityFqn);
            }
            $owningInterfaceFqn = $this->getOwningInterfaceFqn($hasType, $ownedEntityFqn);
            list($interfaceName, , $interfaceSubDirsNoEntities) = $this->parseFullyQualifiedName($owningInterfaceFqn);
            $owningInterfacePath        = $this->getPathFromNameAndSubDirs($interfaceName, $interfaceSubDirsNoEntities);
            $reciprocatingInterfacePath = \str_replace(
                'Has'.$ownedHasName,
                'Reciprocates'.$reciprocatedHasName,
                $owningInterfacePath
            );

            return [
                $owningTraitPath,
                $owningInterfacePath,
                $reciprocatingInterfacePath,
            ];
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
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
     * @param string $hasType
     *
     * @throws \InvalidArgumentException
     */
    protected function validateHasType(string $hasType)
    {
        if (!\in_array($hasType, static::HAS_TYPES, true)) {
            throw new \InvalidArgumentException(
                'Invalid $hasType '.$hasType.', must be one of: '
                .\print_r(static::HAS_TYPES, true)
            );
        }
    }

    /**
     * Set a relationship from one Entity to Another Entity.
     *
     * Also used internally to set the reciprocal side. Uses an undocumented 4th bool parameter to kill recursion.
     *
     * @param string $owningEntityFqn
     * @param string $hasType
     * @param string $ownedEntityFqn
     *
     * @throws \InvalidArgumentException
     * @throws DoctrineStaticMetaException
     */
    public function setEntityHasRelationToEntity(
        string $owningEntityFqn,
        string $hasType,
        string $ownedEntityFqn
    ) {
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
            $owningClassPath = $this->getPathFromNameAndSubDirs($owningClass, $owningClassSubDirs);
            $this->useRelationTraitInClass($owningClassPath, $owningTraitPath);
            $this->useRelationInterfaceInClass($owningClassPath, $owningInterfacePath);
            $this->recriprocateRelation(
                \func_get_args(),
                $owningClassPath,
                $reciprocatingInterfacePath
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__, $e->getCode(), $e);
        }
    }


    /**
     * Now that we have set the relation, its time to do the reciprocal side
     *
     * @param array  $setRelationArgs
     * @param string $owningClassPath
     * @param string $reciprocatingInterfacePath
     *
     * @throws \InvalidArgumentException
     * @throws DoctrineStaticMetaException
     */
    protected function recriprocateRelation(
        array $setRelationArgs,
        string $owningClassPath,
        string $reciprocatingInterfacePath
    ): void {
        if (\count($setRelationArgs) === 4 && $setRelationArgs[3] === false) {
            return;
        }
        list($owningEntityFqn, $hasType, $ownedEntityFqn) = $setRelationArgs;
        if (\in_array($hasType, self::HAS_TYPES_RECIPROCATED, true)) {
            $this->useRelationInterfaceInClass($owningClassPath, $reciprocatingInterfacePath);
            $inverseType = $this->getInverseHasType($hasType);
            //pass in an extra false arg at the end to kill recursion, internal use only
            $this->setEntityHasRelationToEntity($ownedEntityFqn, $inverseType, $owningEntityFqn, false);
        }
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
            case self::HAS_MANY_TO_MANY:
                return \str_replace(
                    self::PREFIX_OWNING,
                    self::PREFIX_INVERSE,
                    $hasType
                );

            case self::HAS_INVERSE_ONE_TO_ONE:
            case self::HAS_INVERSE_MANY_TO_MANY:
                return \str_replace(
                    self::PREFIX_INVERSE,
                    self::PREFIX_OWNING,
                    $hasType
                );

            case self::HAS_MANY_TO_ONE:
                return self::HAS_ONE_TO_MANY;

            case self::HAS_ONE_TO_MANY:
                return self::HAS_MANY_TO_ONE;

            default:
                throw new DoctrineStaticMetaException(
                    'invalid $hasType '.$hasType.' when trying to set the inverted relation'
                );
        }
    }


    /**
     * @param string $path
     * @param string $singular
     * @param string $plural
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    protected function renamePathBasenameSingularOrPlural(
        string $path,
        string $singular,
        string $plural
    ): string {
        $find     = self::FIND_ENTITY_NAME;
        $replace  = $singular;
        $basename = \basename($path);
        if (false !== \strpos($basename, self::FIND_ENTITY_NAME_PLURAL)) {
            $find    = self::FIND_ENTITY_NAME_PLURAL;
            $replace = $plural;
        }

        return $this->renamePathBasename($find, $replace, $path);
    }
}

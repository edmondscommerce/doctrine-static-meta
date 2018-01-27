<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;
use SplFileInfo;

class RelationsGenerator extends AbstractGenerator
{
    const PREFIX_OWNING = 'Owning';

    const PREFIX_INVERSE = 'Inverse';

    const PREFIX_UNIDIRECTIONAL = 'Unidirectional';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityOwningOneToOne.php
     */
    const HAS_ONE_TO_ONE = self::PREFIX_OWNING . 'OneToOne';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityInverseOneToOne.php
     */
    const HAS_INVERSE_ONE_TO_ONE = self::PREFIX_INVERSE . 'OneToOne';

    /**
     * OneToMany - One instance of the current Entity has Many instances (references) to the refered Entity.
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    const HAS_ONE_TO_MANY = 'OneToMany';

    /**
     * OneToMany - One instance of the current Entity has Many instances (references) to the refered Entity.
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    const HAS_UNIDIRECTIONAL_ONE_TO_MANY = self::PREFIX_UNIDIRECTIONAL . 'OneToMany';

    /**
     * OneToMany - One instance of the current Entity has Many instances (references) to the refered Entity.
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    const HAS_INVERSE_ONE_TO_MANY = self::PREFIX_INVERSE . 'OneToMany';


    /**
     * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    const HAS_MANY_TO_ONE = self::PREFIX_OWNING . 'ManyToOne';

    /**
     * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
     *
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    const HAS_UNIDIRECTIONAL_MANY_TO_ONE = self::PREFIX_UNIDIRECTIONAL . 'ManyToOne';

    /**
     * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    const HAS_INVERSE_MANY_TO_ONE = self::PREFIX_INVERSE . 'ManyToOne';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOwningManyToMany.php
     */
    const HAS_MANY_TO_MANY = self::PREFIX_OWNING . 'ManyToMany';

    /**
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesInverseManyToMany.php
     */
    const HAS_INVERSE_MANY_TO_MANY = self::PREFIX_INVERSE . 'ManyToMany';

    /**
     * The full list of possible relation types
     */
    const HAS_TYPES = [
        self::HAS_ONE_TO_ONE,
        self::HAS_INVERSE_ONE_TO_ONE,
        self::HAS_ONE_TO_MANY,
        self::HAS_UNIDIRECTIONAL_ONE_TO_MANY,
        self::HAS_INVERSE_ONE_TO_MANY,
        self::HAS_MANY_TO_ONE,
        self::HAS_UNIDIRECTIONAL_MANY_TO_ONE,
        self::HAS_INVERSE_MANY_TO_ONE,
        self::HAS_MANY_TO_MANY,
        self::HAS_INVERSE_MANY_TO_MANY
    ];

    /**
     * Of the full list, which ones will be automatically reciprocated in the generated code
     */
    const HAS_TYPES_RECIPROCATED = [
        self::HAS_ONE_TO_ONE,
        self::HAS_ONE_TO_MANY,
        self::HAS_INVERSE_ONE_TO_MANY,
        self::HAS_MANY_TO_ONE,
        self::HAS_MANY_TO_MANY
    ];

    /**
     * Of the full list, which ones are a plural relationship, i.e they have multiple of the related entity
     */
    const HAS_TYPES_PLURAL = [
        self::HAS_MANY_TO_MANY,
        self::HAS_INVERSE_MANY_TO_MANY,
        self::HAS_ONE_TO_MANY,
        self::HAS_UNIDIRECTIONAL_ONE_TO_MANY,
        self::HAS_INVERSE_ONE_TO_MANY,
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
                    realpath(AbstractGenerator::RELATIONS_TEMPLATE_PATH)
                    ,
                    \RecursiveDirectoryIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($recursiveIterator as $path => $fileInfo) {
                $relativePath = rtrim(
                    $this->getFileSystem()->makePathRelative($path, AbstractGenerator::RELATIONS_TEMPLATE_PATH),
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
     * @throws \Exception
     */
    public function generateRelationCodeForEntity(string $entityFqn)
    {
        list($className, , $subDirsNoEntities) = $this->parseFullyQualifiedName($entityFqn);
        $subDirsNoEntities    = array_slice($subDirsNoEntities, 2);
        $destinationDirectory = $this->pathToProjectSrcRoot
            . '/' . $this->srcSubFolderName
            . '/' . $this->entitiesFolderName
            . '/Relations/' . implode(
                '/',
                $subDirsNoEntities
            )
            . '/' . $className;

        $this->copyTemplateDirectoryAndGetPath(
            AbstractGenerator::RELATIONS_TEMPLATE_PATH,
            $destinationDirectory
        );

        $plural                = ucfirst($entityFqn::getPlural());
        $singular              = ucfirst($entityFqn::getSingular());
        $namespaceNoEntities   = implode('\\', $subDirsNoEntities);
        $singularWithNamespace = ltrim(
            $namespaceNoEntities . '\\' . $singular,
            '\\'
        );
        $pluralWithNamespace   = ltrim(
            $namespaceNoEntities . '\\' . $plural,
            '\\'
        );
        $entitiesNamespace     = $this->projectRootNamespace . '\\' . $this->entitiesFolderName;
        $dirsToRename          = [];
        $filesCreated          = [];
        //update file contents apart from namespace
        foreach ($this->getRelativePathRelationsGenerator() as $path => $fileInfo) {
            $realPath = realpath("$destinationDirectory/$path");
            if (false === $realPath) {
                throw new \RuntimeException("path $destinationDirectory/$path does not exist");
            }
            $path = $realPath;
            if (!$fileInfo->isDir()) {
                $this->findReplace(
                    'use ' . self::FIND_NAMESPACE . '\\' . self::FIND_ENTITY_NAME . ';',
                    "use $entityFqn;",
                    $path
                );
                $this->findReplace(
                    '%use(.+?)Relations\\\TemplateEntity(.+?);%',
                    'use ${1}Relations\\' . $singularWithNamespace . '${2};',
                    $path,
                    true
                );
                $this->findReplace(
                    '%use(.+?)Relations\\\TemplateEntity(.+?);%',
                    'use ${1}Relations\\' . $pluralWithNamespace . '${2};',
                    $path,
                    true
                );

                $this->replaceEntityName($singular, $path);
                $this->replacePluralEntityName($plural, $path);
                $this->replaceNamespace($entitiesNamespace, $path);
                $filesCreated[] = $this->renamePathBasenameSingularOrPlural($path, $singular, $plural);;
            } else {
                $dirsToRename[] = $path;
            }
        }
        //update directory names and update file created paths accordingly
        foreach ($dirsToRename as $dirPath) {
            $updateDairPath = $this->renamePathBasenameSingularOrPlural($dirPath, $singular, $plural);
            foreach ($filesCreated as $k => $filePath) {
                $filesCreated[$k] = str_replace($dirPath, $updateDairPath, $filePath);
            }
        }
        //now path is totally sorted, update namespace based on path
        foreach ($filesCreated as $filePath) {
            $this->setNamespaceFromPath($filePath);
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
                'declareStrictTypes' => true
            ]
        );
        $class     = PhpClass::fromFile($classPath);
        $interface = PhpInterface::fromFile($interfacePath);
        $class->addInterface($interface);
        $generatedClass = $generator->generate($class);
        file_put_contents($classPath, $generatedClass);
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
                'declareStrictTypes' => true
            ]
        );
        $class     = PhpClass::fromFile($classPath);
        $trait     = PhpTrait::fromFile($traitPath);
        $class->addTrait($trait);
        $generatedClass = $generator->generate($class);
        file_put_contents($classPath, $generatedClass);
    }

    /**
     * Get the absolute path for the owning trait for the specified relation type
     * Will ensure that the trait exists
     *
     * @param string $hasType
     * @param string $ownedEntityFqn
     *
     * @return array [ $owningTraitPath, $owningInterfacePath, $reciprocatingInterfacePath ]
     * @throws \Exception
     */
    protected function getPathsForOwningTraitAndInterface(string $hasType, string $ownedEntityFqn): array
    {
        list($ownedClassName, , $ownedSubDirectories) = $this->parseFullyQualifiedName($ownedEntityFqn);
        if (in_array(
            $hasType,
            static::HAS_TYPES_PLURAL
        )) {
            $ownedHasName = ucfirst(MappingHelper::getPluralForFqn($ownedEntityFqn));
        } else {
            $ownedHasName = ucfirst(MappingHelper::getSingularForFqn($ownedEntityFqn));
        }
        $reciprocatedHasName = ucfirst(MappingHelper::getSingularForFqn($ownedEntityFqn));
        $traitSubDirectories = array_slice($ownedSubDirectories, 2);
        $owningTraitFqn      = $this->projectRootNamespace
            . '\\' . $this->entitiesFolderName
            . '\\Relations\\' . implode('\\', $traitSubDirectories)
            . '\\' . $ownedClassName . '\\Traits\\Has' . $ownedHasName
            . '\\Has' . $ownedHasName . $this->stripPrefixFromHasType($hasType);
        list($traitName, , $traitSubDirsNoEntities) = $this->parseFullyQualifiedName($owningTraitFqn);
        $owningTraitPath = $this->getPathFromNameAndSubDirs($traitName, $traitSubDirsNoEntities);
        if (!file_exists($owningTraitPath)) {
            $this->generateRelationCodeForEntity($ownedEntityFqn);
        }
        $owningInterfaceFqn = $this->projectRootNamespace
            . '\\' . $this->entitiesFolderName
            . '\\Relations\\' . implode('\\', $traitSubDirectories) . '\\' . $ownedClassName
            . '\\Interfaces\\Has' . $ownedHasName;
        list($interfaceName, , $interfaceSubDirsNoEntities) = $this->parseFullyQualifiedName($owningInterfaceFqn);
        $owningInterfacePath        = $this->getPathFromNameAndSubDirs($interfaceName, $interfaceSubDirsNoEntities);
        $reciprocatingInterfacePath = str_replace('Has' . $ownedHasName, 'Reciprocates' . $reciprocatedHasName, $owningInterfacePath);
        return [$owningTraitPath, $owningInterfacePath, $reciprocatingInterfacePath];
    }

    public function setEntityHasRelationToEntity(
        string $owningEntityFqn,
        string $hasType,
        string $ownedEntityFqn
    )
    {
        if (!in_array($hasType, static::HAS_TYPES)) {
            throw new \InvalidArgumentException(
                'Invalid $hasType ' . $hasType . ', must be one of: '
                . print_r(static::HAS_TYPES, true)
            );
        }
        list(
            $owningTraitPath,
            $owningInterfacePath,
            $reciprocatingInterfacePath
            ) = $this->getPathsForOwningTraitAndInterface(
            $hasType,
            $ownedEntityFqn
        );
        list($owningClass, , $owningClassSubDirs) = $this->parseFullyQualifiedName($owningEntityFqn);
        $owningClassPath = $this->getPathFromNameAndSubDirs($owningClass, $owningClassSubDirs);
        $this->useRelationTraitInClass($owningClassPath, $owningTraitPath);
        $this->useRelationInterfaceInClass($owningClassPath, $owningInterfacePath);
        $this->useRelationInterfaceInClass($owningClassPath, $reciprocatingInterfacePath);
        //pass in an extra false arg at the end to kill recursion, internal use only
        $args = func_get_args();
        if (count($args) === 4 && $args[3] === false) {
            return;
        }
        if (in_array($hasType, self::HAS_TYPES_RECIPROCATED)) {
            switch ($hasType) {
                case static::HAS_ONE_TO_ONE:
                case static::HAS_MANY_TO_MANY:
                    $inverseType = str_replace('Owning', 'Inverse', $hasType);
                    break;
                case static::HAS_MANY_TO_ONE:
                    $inverseType = static::HAS_ONE_TO_MANY;
                    break;
                case static::HAS_ONE_TO_MANY:
                    $inverseType = static::HAS_MANY_TO_ONE;
                    break;
                default:
                    throw new \Exception('invalid $hasType ' . $hasType . ' when trying to set the inverted relation');
            }
            $this->setEntityHasRelationToEntity($ownedEntityFqn, $inverseType, $owningEntityFqn, false);
        }
    }

    /**
     * Inverse and Unidrectional hasTypes use the standard template without the prefix
     * The exclusion ot this are the ManyToMany and OneToOne relations
     *
     * @param string $hasType
     *
     * @return string
     */
    protected function stripPrefixFromHasType(string $hasType): string
    {
        foreach (['ManyToMany', 'OneToOne'] as $noStrip) {
            if (false !== strpos($hasType, $noStrip)) {
                return $hasType;
            }
        }

        foreach (['OneToMany', 'ManyToOne'] as $stripAll) {
            if (false !== strpos($hasType, $stripAll)) {
                return str_replace(
                    [
                        self::PREFIX_OWNING,
                        self::PREFIX_INVERSE
                    ],
                    '',
                    $hasType
                );
            }
        }

        return str_replace(
            [
                self::PREFIX_INVERSE
            ],
            '',
            $hasType
        );
    }


    protected function renamePathBasenameSingularOrPlural(
        string $path,
        string $singular,
        string $plural): string
    {
        $find     = self::FIND_ENTITY_NAME;
        $replace  = $singular;
        $basename = basename($path);
        if (false !== strpos($basename, self::FIND_ENTITY_NAME_PLURAL)) {
            $find    = self::FIND_ENTITY_NAME_PLURAL;
            $replace = $plural;
        }
        $updatedPath = $this->renamePathBasename($find, $replace, $path);

        return $updatedPath;
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
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
     * One to Many - The related entity has many of the current entity
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    const HAS_ONE_TO_MANY = 'OneToMany';

    /**
     * One to Many - The related entity has many of the current entity
     *
     * The Unidirectional bit is purely to show that we don't reciprocate. The template used is the one without
     * Unidirectional
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    const HAS_UNIDIRECTIONAL_ONE_TO_MANY = self::PREFIX_UNIDIRECTIONAL . 'OneToMany';

    /**
     * One to Many - The related entity has many of the current entity
     *
     * The Inverse bit is purely to show that we don't reciprocate. The template used is the one without Inverse
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntities/HasTemplateEntitiesOneToMany.php
     */
    const HAS_INVERSE_ONE_TO_MANY = self::PREFIX_INVERSE . 'OneToMany';


    /**
     * Many to One - The current entity has many of the related entity
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    const HAS_MANY_TO_ONE = self::PREFIX_OWNING . 'ManyToOne';

    /**
     * Many to One - The current entity has many of the related entity
     *
     * The Unidirectional bit is purely to show that we don't reciprocate. The template used is the one without
     * Unidirectional
     *
     * @see codeTemplates/src/Entities/Traits/Relations/TemplateEntity/HasTemplateEntity/HasTemplateEntityManyToOne.php
     */
    const HAS_UNIDIRECTIONAL_MANY_TO_ONE = self::PREFIX_UNIDIRECTIONAL . 'ManyToOne';

    /**
     * Many to One - The current entity has many of the related entity
     *
     * The Inverse bit is purely to show that we don't reciprocate. The template used is the one without Inverse
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
    const RELATION_TYPES = [
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
     * Of the full list, which ones will be automatically reciprocated in the code generation
     */
    const RELATION_TYPES_RECIPROCATED = [
        self::HAS_ONE_TO_ONE,
        self::HAS_ONE_TO_MANY,
        self::HAS_INVERSE_ONE_TO_MANY,
        self::HAS_MANY_TO_ONE,
        self::HAS_MANY_TO_MANY
    ];

    /**
     * Of the full list, which ones are a plural relationship, i.e they have multiple of the related entity
     */
    const RELATION_TYPES_PLURAL = [
        self::HAS_MANY_TO_MANY,
        self::HAS_INVERSE_MANY_TO_MANY,
        self::HAS_MANY_TO_ONE,
        self::HAS_UNIDIRECTIONAL_MANY_TO_ONE,
        self::HAS_INVERSE_MANY_TO_ONE,
    ];

    public function generateRelationTraitsForEntity(string $fullyQualifiedName)
    {
        list($className, , $subDirsNoEntities) = $this->parseFullyQualifiedName($fullyQualifiedName);
        $subDirsNoEntities = array_slice($subDirsNoEntities, 2);

        $destinationDirectory = $this->pathToProjectSrcRoot
            . '/' . $this->srcSubFolderName
            . '/' . $this->entitiesFolderName
            . '/Traits/Relations/' . implode(
                '/',
                $subDirsNoEntities
            )
            . '/' . $className;
        $this->copyTemplateDirectoryAndGetPath(
            AbstractGenerator::RELATIONS_TEMPLATE_PATH,
            $destinationDirectory
        );
        $iterator              = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                realpath($destinationDirectory),
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $plural                = ucfirst($fullyQualifiedName::getPlural());
        $singular              = ucfirst($fullyQualifiedName::getSingular());
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

        /**
         * @var SplFileInfo[] $iterator
         */
        $dirsToRename = [];
        //update file contents apart from namespace
        foreach ($iterator as $path => $i) {
            if (!$i->isDir()) {
                $this->findReplace(
                    'use ' . self::FIND_NAMESPACE . '\\' . self::FIND_ENTITY_NAME . ';',
                    "use $fullyQualifiedName;",
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
                $this->renamePathSingularOrPlural($path, $singular, $plural);
            } else {
                $dirsToRename[] = $path;
            }
        }
        //update directory names
        foreach ($dirsToRename as $path) {
            $this->renamePathSingularOrPlural($path, $singular, $plural);
        }
        //now path is totally sorted, update namespace based on path
        foreach ($iterator as $path => $i) {
            if (!$i->isDir()) {
                $this->setNamespaceFromPath($path);
            }
        }
    }

    protected function useRelationTraitInClass(string $entityFqn, string $traitPath)
    {
        $generator = new CodeFileGenerator(
            [
                'generateDocblock'   => false,
                'declareStrictTypes' => true
            ]
        );
        list($className, , $classSubDirsNoEntities) = $this->parseFullyQualifiedName($entityFqn);
        $classPath = $this->getPathForClassOrTrait($className, $classSubDirsNoEntities);
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
     * @return string
     */
    protected function getOwningTraitPathRelation(string $hasType, string $ownedEntityFqn): string
    {
        list($ownedClassName, , $ownedSubDirectories) = $this->parseFullyQualifiedName($ownedEntityFqn);
        if (in_array(
            $hasType,
            static::RELATION_TYPES_PLURAL
        )) {
            $ownedHasName = ucfirst(MappingHelper::getPluralForFqn($ownedEntityFqn));
        } else {
            $ownedHasName = ucfirst(MappingHelper::getSingularForFqn($ownedEntityFqn));
        }
        $traitSubDirectories = array_slice($ownedSubDirectories, 2);
        $owningTraitFqn      = $this->projectRootNamespace
            . '\\' . $this->entitiesFolderName
            . '\\Traits\\Relations\\' . implode('\\', $traitSubDirectories)
            . '\\' . $ownedClassName . '\\Has' . $ownedHasName
            . '\\Has' . $ownedHasName . $this->stripPrefixFromHasType($hasType);
        list($traitName, , $traitSubDirsNoEntities) = $this->parseFullyQualifiedName($owningTraitFqn);
        $owningTraitPath = $this->getPathForClassOrTrait($traitName, $traitSubDirsNoEntities);
        if (!file_exists($owningTraitPath)) {
            $this->generateRelationTraitsForEntity($ownedEntityFqn);
        }
        return $owningTraitPath;
    }

    public function setEntityHasRelationToEntity(
        string $owningEntityFqn,
        string $hasType,
        string $ownedEntityFqn
    )
    {
        if (!in_array($hasType, static::RELATION_TYPES)) {
            throw new \InvalidArgumentException(
                'Invalid $hasType ' . $hasType . ', must be one of: '
                . print_r(static::RELATION_TYPES, true)
            );
        }
        $owningTraitPath = $this->getOwningTraitPathRelation($hasType, $ownedEntityFqn);
        $this->useRelationTraitInClass($owningEntityFqn, $owningTraitPath);
        //pass in an extra false arg at the end to kill recursion, internal use only
        $args = func_get_args();
        if (count($args) === 4 && $args[3] === false) {
            return;
        }
        if (in_array($hasType, self::RELATION_TYPES_RECIPROCATED)) {
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
                        self::PREFIX_UNIDIRECTIONAL,
                        self::PREFIX_INVERSE
                    ],
                    '',
                    $hasType
                );
            }
        }

        return str_replace(
            [
                self::PREFIX_UNIDIRECTIONAL,
                self::PREFIX_INVERSE
            ],
            '',
            $hasType
        );
    }

    protected function renamePathSingularOrPlural(
        string $path,
        string $singular,
        string $plural): AbstractGenerator
    {
        $find     = self::FIND_ENTITY_NAME;
        $replace  = $singular;
        $basename = basename($path);
        if (false !== strpos($basename, self::FIND_ENTITY_NAME_PLURAL)) {
            $find    = self::FIND_ENTITY_NAME_PLURAL;
            $replace = $plural;
        }
        $this->replaceInPath($find, $replace, $path);

        return $this;
    }
}

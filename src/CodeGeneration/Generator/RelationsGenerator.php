<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpTrait;
use SplFileInfo;

class RelationsGenerator extends AbstractGenerator
{
    const HAS_ONE_TO_ONE = 'OwningOneToOne';

    const HAS_INVERSE_ONE_TO_ONE = 'InverseOneToOne';

    const HAS_ONE_TO_MANY = 'OwningOneToMany';

    const HAS_UNIDIRECTIONAL_ONE_TO_MANY = 'OneToMany';

    const HAS_INVERSE_ONE_TO_MANY = 'OneToMany';

    const HAS_MANY_TO_ONE = 'OwningManyToOne';

    const HAS_UNIDIRECTIONAL_MANY_TO_ONE = 'ManyToOne';

    const HAS_INVERSE_MANY_TO_ONE = 'ManyToOne';

    const HAS_MANY_TO_MANY = 'OwningManyToMany';

    const HAS_INVERSE_MANY_TO_MANY = 'InverseManyToMany';

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

    public function generateRelationTraitsForEntity(string $fullyQualifiedName)
    {

        list($className, $namespace, $subDirsNoEntities) = $this->parseFullyQualifiedName($fullyQualifiedName);
        $this->requireEntity($className, $subDirsNoEntities);
        $singular = ucfirst($fullyQualifiedName::getSingular());
        $plural = ucfirst($fullyQualifiedName::getPlural());
        array_shift($subDirsNoEntities);
        $destinationDirectory = $this->pathToProjectRoot . '/' . $this->entitiesFolderName . '/Traits/Relations/' . implode(
                '/',
                $subDirsNoEntities
            ) . '/' . $className;
        $this->copyTemplateDirectoryAndGetPath(
            AbstractGenerator::RELATIONS_TEMPLATE_PATH,
            $destinationDirectory
        );
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                realpath($destinationDirectory),
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        /**
         * @var SplFileInfo[] $iterator
         */
        $dirsToRename = [];
        foreach ($iterator as $path => $i) {
            if (!$i->isDir()) {
                $this->replaceEntityName($singular, $path);
                $this->replacePluralEntityName($plural, $path);
                $this->replaceNamespace($namespace, $path);
                $this->renamePathSingularOrPlural($path, $singular, $plural);
            } else {
                $dirsToRename[] = $path;
            }
        }
        foreach ($dirsToRename as $path) {
            $this->renamePathSingularOrPlural($path, $singular, $plural);
        }
    }

    protected function useTraitInClass(string $classFqn, string $traitFqn)
    {
        $generator = new CodeFileGenerator([
            'generateDocblock' => false,
            'declareStrictTypes' => true
        ]);
        list($className, $classNamespace, $classSubDirsNoEntities) = $this->parseFullyQualifiedName($classFqn);
        $classPath = $this->getPathForClass($className, $classSubDirsNoEntities);
        $class = PhpClass::fromFile($classPath);
        list($traitName, $traitNamespace, $traitSubDirsNoEntities) = $this->parseFullyQualifiedName($traitFqn);
        $traitPath = $this->getPathFortrait($traitName, $traitSubDirsNoEntities);
        $trait = PhpTrait::fromFile($traitPath);
        $class->addTrait($trait);
        $generatedClass = $generator->generate($class);
        file_put_contents($classPath, $generatedClass);
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
        list($ownedClassName, , $ownedSubDirectories) = $this->parseFullyQualifiedName($ownedEntityFqn);
        $this->requireEntity($ownedClassName, $ownedSubDirectories);
        $ownedHasName = in_array(
            $hasType,
            [
                static::HAS_MANY_TO_MANY,
                static::HAS_INVERSE_MANY_TO_MANY,
                static::HAS_MANY_TO_ONE
            ]
        ) ?
            $ownedClassName::getPlural()
            : $ownedClassName::getSingular();

        $owningTraitFqn = $this->projectRootNamespace . '\\' . $this->entitiesFolderName
            . '\\Traits\\Relations\\' . $ownedClassName . '\\Has' . $ownedHasName . '\\'
            . '\\Has' . $ownedHasName . $hasType;
        $this->useTraitInClass($owningEntityFqn, $owningTraitFqn);

        if (0 === strpos($hasType, 'Owning')) {
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
            $this->setEntityHasRelationToEntity($ownedEntityFqn, $inverseType, $owningEntityFqn);
        }
    }

    protected function renamePathSingularOrPlural(string $path, string $singular, string $plural): AbstractGenerator
    {
        $find = self::FIND_ENTITY_NAME;
        $replace = $singular;
        $basename = basename($path);
        if (false !== strpos($basename, self::FIND_ENTITY_NAME_PLURAL)) {
            $find = self::FIND_ENTITY_NAME_PLURAL;
            $replace = $plural;
        }
        $this->replaceInPath($find, $replace, $path);

        return $this;
    }
}

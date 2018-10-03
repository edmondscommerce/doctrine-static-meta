<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Builder;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDataTransferObjectsForAllEntitiesAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Modification\CodeGenClassTypeFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover;
use Nette\PhpGenerator\Constant;
use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * Class Builder
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Builder
 * @SuppressWarnings(PHPMD)
 */
class Builder
{

    /**
     * @var EntityGenerator
     */
    protected $entityGenerator;
    /**
     * @var FieldGenerator
     */
    protected $fieldGenerator;
    /**
     * @var EntityFieldSetter
     */
    protected $fieldSetter;
    /**
     * @var RelationsGenerator
     */
    protected $relationsGenerator;
    /**
     * @var ArchetypeEmbeddableGenerator
     */
    protected $archetypeEmbeddableGenerator;
    /**
     * @var EntityEmbeddableSetter
     */
    protected $embeddableSetter;
    /**
     * @var CodeHelper
     */
    protected $codeHelper;
    /**
     * @var UnusedRelationsRemover
     */
    protected $unusedRelationsRemover;
    /**
     * @var CreateDataTransferObjectsForAllEntitiesAction
     */
    private $dataTransferObjectsForAllEntitiesAction;
    /**
     * @var CodeGenClassTypeFactory
     */
    private $codeGenClassTypeFactory;

    public function __construct(
        EntityGenerator $entityGenerator,
        FieldGenerator $fieldGenerator,
        EntityFieldSetter $fieldSetter,
        RelationsGenerator $relationsGenerator,
        ArchetypeEmbeddableGenerator $archetypeEmbeddableGenerator,
        EntityEmbeddableSetter $embeddableSetter,
        CodeHelper $codeHelper,
        UnusedRelationsRemover $unusedRelationsRemover,
        CreateDataTransferObjectsForAllEntitiesAction $dataTransferObjectsForAllEntitiesAction,
        CodeGenClassTypeFactory $codeGenClassTypeFactory
    ) {
        $this->entityGenerator                         = $entityGenerator;
        $this->fieldGenerator                          = $fieldGenerator;
        $this->fieldSetter                             = $fieldSetter;
        $this->relationsGenerator                      = $relationsGenerator;
        $this->archetypeEmbeddableGenerator            = $archetypeEmbeddableGenerator;
        $this->embeddableSetter                        = $embeddableSetter;
        $this->codeHelper                              = $codeHelper;
        $this->unusedRelationsRemover                  = $unusedRelationsRemover;
        $this->dataTransferObjectsForAllEntitiesAction = $dataTransferObjectsForAllEntitiesAction;
        $this->codeGenClassTypeFactory                 = $codeGenClassTypeFactory;
    }

    public function setPathToProjectRoot(string $pathToProjectRoot): self
    {
        $this->entityGenerator->setPathToProjectRoot($pathToProjectRoot);
        $this->fieldGenerator->setPathToProjectRoot($pathToProjectRoot);
        $this->fieldSetter->setPathToProjectRoot($pathToProjectRoot);
        $this->relationsGenerator->setPathToProjectRoot($pathToProjectRoot);
        $this->archetypeEmbeddableGenerator->setPathToProjectRoot($pathToProjectRoot);
        $this->dataTransferObjectsForAllEntitiesAction->setProjectRootDirectory($pathToProjectRoot);

        return $this;
    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->entityGenerator->setProjectRootNamespace($projectRootNamespace);
        $this->fieldGenerator->setProjectRootNamespace($projectRootNamespace);
        $this->fieldSetter->setProjectRootNamespace($projectRootNamespace);
        $this->relationsGenerator->setProjectRootNamespace($projectRootNamespace);
        $this->archetypeEmbeddableGenerator->setProjectRootNamespace($projectRootNamespace);
        $this->dataTransferObjectsForAllEntitiesAction->setProjectRootNamespace($projectRootNamespace);

        return $this;
    }

    /**
     * @return EntityGenerator
     */
    public function getEntityGenerator(): EntityGenerator
    {
        return $this->entityGenerator;
    }

    /**
     * @return FieldGenerator
     */
    public function getFieldGenerator(): FieldGenerator
    {
        return $this->fieldGenerator;
    }

    /**
     * @return EntityFieldSetter
     */
    public function getFieldSetter(): EntityFieldSetter
    {
        return $this->fieldSetter;
    }

    /**
     * @return RelationsGenerator
     */
    public function getRelationsGenerator(): RelationsGenerator
    {
        return $this->relationsGenerator;
    }

    /**
     * @return ArchetypeEmbeddableGenerator
     */
    public function getArchetypeEmbeddableGenerator(): ArchetypeEmbeddableGenerator
    {
        return $this->archetypeEmbeddableGenerator;
    }

    /**
     * @return EntityEmbeddableSetter
     */
    public function getEmbeddableSetter(): EntityEmbeddableSetter
    {
        return $this->embeddableSetter;
    }

    /**
     * Generate all the data transfer objects
     *
     * Should be done as a final step
     *
     * @return Builder
     */
    public function generateDataTransferObjectsForAllEntities(): self
    {
        $this->dataTransferObjectsForAllEntitiesAction->run();

        return $this;
    }

    /**
     * @param array $entityFqns
     *
     * @return Builder
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function generateEntities(array $entityFqns): self
    {
        foreach ($entityFqns as $entityFqn) {
            $this->entityGenerator->generateEntity($entityFqn);
        }

        return $this;
    }

    /**
     * @param array $entityRelationEntity
     *
     * @return Builder
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function setEntityRelations(array $entityRelationEntity): self
    {
        foreach ($entityRelationEntity as list($owningEntityFqn, $hasType, $ownedEntityFqn)) {
            $this->relationsGenerator->setEntityHasRelationToEntity($owningEntityFqn, $hasType, $ownedEntityFqn);
        }

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return array $traitFqns
     */
    public function generateFields(array $fields): array
    {
        $traitFqns = [];
        foreach ($fields as list($fieldFqn, $fieldType)) {
            try {
                $traitFqns[] = $this->fieldGenerator->generateField($fieldFqn, $fieldType);
            } catch (\Exception $e) {
                throw new \RuntimeException(
                    'Failed building field with $fieldFqn: ' . $fieldFqn . ' and $fieldType ' . $fieldType,
                    $e->getCode(),
                    $e
                );
            }
        }

        return $traitFqns;
    }

    /**
     * @param string $entityFqn
     * @param array  $fieldFqns
     *
     * @return Builder
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function setFieldsToEntity(string $entityFqn, array $fieldFqns): self
    {
        foreach ($fieldFqns as $fieldFqn) {
            $this->fieldSetter->setEntityHasField($entityFqn, $fieldFqn);
        }

        return $this;
    }

    /**
     * @param array $embeddables
     *
     * @return array $traitFqns
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function generateEmbeddables(array $embeddables): array
    {
        $traitFqns = [];
        foreach ($embeddables as list($archetypeEmbeddableObjectFqn, $newEmbeddableObjectClassName)) {
            $traitFqns[] = $this->archetypeEmbeddableGenerator->createFromArchetype(
                $archetypeEmbeddableObjectFqn,
                $newEmbeddableObjectClassName
            );
        }

        return $traitFqns;
    }

    /**
     * @param string $entityFqn
     * @param array  $embeddableTraitFqns
     *
     * @return Builder
     */
    public function setEmbeddablesToEntity(string $entityFqn, array $embeddableTraitFqns): self
    {
        foreach ($embeddableTraitFqns as $embeddableTraitFqn) {
            $this->embeddableSetter->setEntityHasEmbeddable($entityFqn, $embeddableTraitFqn);
        }

        return $this;
    }

    public function setEnumOptionsOnInterface(string $interfaceFqn, array $options): void
    {
        $pathToInterface = ReflectionClass::createFromName($interfaceFqn)->getFileName();
        $basename        = basename($pathToInterface);
        $classy          = substr($basename, 0, strpos($basename, 'FieldInterface'));
        $consty          = $this->codeHelper->consty($classy);
        $interface       = $this->codeGenClassTypeFactory->createFromFqn($interfaceFqn);
        $constants       = $interface->getConstants();
        $constants->map(function (Constant $constant) use ($interface, $consty) {
            if (0 === strpos($constant->getName(), $consty . '_OPTION')) {
                $interface->removeConstant($constant->getName());
            }
            if (0 === strpos($constant->getName(), 'DEFAULT')) {
                $interface->removeConstant($constant->getName());
            }
        });
        $optionConsts = [];
        foreach ($options as $option) {
            $name           = \str_replace(
                '__',
                '_',
                $consty . '_OPTION_' . $this->codeHelper->consty(
                    \str_replace(' ', '_', $option)
                )
            );
            $optionConsts[] = 'self::' . $name;
            $interface->addConstant($name, $option);
        }
        $interface->addConstant(
            $consty . '_OPTIONS',
            '[' . implode(",\n", $optionConsts) . ']'
        );
        $interface->addConstant(
            'DEFAULT_' . $consty,
            current($optionConsts)
        );
        $this->codeHelper->generate($interface, $pathToInterface);
    }

    public function injectTraitInToClass(string $traitFqn, string $classFqn): void
    {
        $classFilePath = $this->getFileName($classFqn);
        $class         = $this->codeGenClassTypeFactory->createFromFqn($classFqn);
        $traits        = $class->getTraits();
        $exists        = array_search($traitFqn, $traits, true);
        if ($exists !== false) {
            return;
        }
        $class->addTrait($traitFqn);
        $this->codeHelper->generate($class, $classFilePath);
    }

    private function getFileName(string $typeFqn): string
    {
        return ReflectionClass::createFromName($typeFqn)->getFileName();
    }

    public function extendInterfaceWithInterface(string $interfaceToExtendFqn, string $interfaceToAddFqn): void
    {
        $toExtendFilePath = $this->getFileName($interfaceToExtendFqn);
        $toExtend         = $this->codeGenClassTypeFactory->createFromFqn($interfaceToExtendFqn);
        if (\in_array($interfaceToAddFqn, $toExtend->getImplements(), true)) {
            return;
        }
        $toExtend->addImplement($interfaceToAddFqn);
        $this->codeHelper->generate($toExtend, $toExtendFilePath);
    }

    public function removeIdTraitFromClass(string $classFqn): void
    {
        $traitFqn = "DSM\\Fields\\Traits\\PrimaryKey\\IdFieldTrait";
        $this->removeTraitFromClass($classFqn, $traitFqn);
    }

    public function removeTraitFromClass(string $classFqn, string $traitFqn): void
    {
        $classPath = $this->getFileName($classFqn);
        $class     = PhpClass::fromFile($classPath);
        $traits    = $class->getTraits();
        if ($class->getUseStatements()->contains($traitFqn) === true) {
            $class->removeUseStatement($traitFqn);
        }
        $index = array_search($traitFqn, $traits, true);
        if ($index === false) {
            $shortNameParts = explode('\\', $traitFqn);
            $shortName      = (string)array_pop($shortNameParts);
            $index          = array_search($shortName, $traits, true);
        }
        if ($index === false) {
            return;
        }
        unset($traits[$index]);
        $reflectionClass = new ReflectionClass(PhpClass::class);
        $property        = $reflectionClass->getProperty('traits');
        $property->setAccessible(true);
        $property->setValue($class, $traits);
        $this->codeHelper->generate($class, $classPath);
    }

    public function removeUnusedRelations(): void
    {
        $this->unusedRelationsRemover->run();
    }
}

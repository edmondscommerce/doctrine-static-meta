<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Builder;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\CopyPhpstormMeta;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\EntityFormatter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpConstant;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;
use ts\Reflection\ReflectionClass;

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
     * @var CreateDtosForAllEntitiesAction
     */
    private $dataTransferObjectsForAllEntitiesAction;
    /**
     * @var EntityFormatter
     */
    private $entityFormatter;
    /**
     * @var CopyPhpstormMeta
     */
    private $copyPhpstormMeta;
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function __construct(
        EntityGenerator $entityGenerator,
        FieldGenerator $fieldGenerator,
        EntityFieldSetter $fieldSetter,
        RelationsGenerator $relationsGenerator,
        ArchetypeEmbeddableGenerator $archetypeEmbeddableGenerator,
        EntityEmbeddableSetter $embeddableSetter,
        CodeHelper $codeHelper,
        UnusedRelationsRemover $unusedRelationsRemover,
        CreateDtosForAllEntitiesAction $dataTransferObjectsForAllEntitiesAction,
        EntityFormatter $entityFormatter,
        Config $config,
        CopyPhpstormMeta $copyPhpstormMeta,
        NamespaceHelper $namespaceHelper
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
        $this->entityFormatter                         = $entityFormatter;
        $this->copyPhpstormMeta                        = $copyPhpstormMeta;
        $this->namespaceHelper                         = $namespaceHelper;

        $this->setPathToProjectRoot($config::getProjectRootDirectory());
    }

    public function setPathToProjectRoot(string $pathToProjectRoot): self
    {
        $this->entityGenerator->setPathToProjectRoot($pathToProjectRoot);
        $this->fieldGenerator->setPathToProjectRoot($pathToProjectRoot);
        $this->fieldSetter->setPathToProjectRoot($pathToProjectRoot);
        $this->relationsGenerator->setPathToProjectRoot($pathToProjectRoot);
        $this->archetypeEmbeddableGenerator->setPathToProjectRoot($pathToProjectRoot);
        $this->unusedRelationsRemover->setPathToProjectRoot($pathToProjectRoot);
        $this->dataTransferObjectsForAllEntitiesAction->setProjectRootDirectory($pathToProjectRoot);
        $this->embeddableSetter->setPathToProjectRoot($pathToProjectRoot);
        $this->entityFormatter->setPathToProjectRoot($pathToProjectRoot);
        $this->copyPhpstormMeta->setPathToProjectRoot($pathToProjectRoot);

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
     * Finalise build - run various steps to wrap up the build and tidy up the codebase
     *
     * @return Builder
     */
    public function finaliseBuild(): self
    {
        $this->dataTransferObjectsForAllEntitiesAction->run();
        $this->entityFormatter->run();
        $this->removeUnusedRelations();
        $this->copyPhpstormMeta->run();

        return $this;
    }

    public function removeUnusedRelations(): void
    {
        $this->unusedRelationsRemover->run();
    }

    /**
     * @param array $entityFqns
     *
     * @return Builder
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function generateEntities(array $entityFqns): self
    {
        $this->setProjectRootNamespace(
            $this->namespaceHelper->getProjectNamespaceRootFromEntityFqn(
                current($entityFqns)
            )
        );
        foreach ($entityFqns as $entityFqn) {
            $this->entityGenerator->generateEntity($entityFqn);
        }

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
        $this->embeddableSetter->setProjectRootNamespace($projectRootNamespace);
        $this->unusedRelationsRemover->setProjectRootNamespace($projectRootNamespace);

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
        $pathToInterface = (new ReflectionClass($interfaceFqn))->getFileName();
        $basename        = basename($pathToInterface);
        $classy          = substr($basename, 0, strpos($basename, 'FieldInterface'));
        $consty          = $this->codeHelper->consty($classy);
        $interface       = PhpInterface::fromFile($pathToInterface);
        $constants       = $interface->getConstants();
        $constants->map(function (PhpConstant $constant) use ($interface, $consty) {
            if (0 === strpos($constant->getName(), $consty . '_OPTION')) {
                $interface->removeConstant($constant);
            }
            if (0 === strpos($constant->getName(), 'DEFAULT')) {
                $interface->removeConstant($constant);
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
            $constant       = new PhpConstant($name, $option);
            $interface->setConstant($constant);
        }
        $interface->setConstant(
            new PhpConstant(
                $consty . '_OPTIONS',
                '[' . implode(",\n", $optionConsts) . ']',
                true
            )
        );
        $interface->setConstant(
            new PhpConstant(
                'DEFAULT_' . $consty,
                current($optionConsts),
                true
            )
        );
        $this->codeHelper->generate($interface, $pathToInterface);
    }

    public function injectTraitInToClass(string $traitFqn, string $classFqn): void
    {
        $classFilePath = $this->getFileName($classFqn);
        $class         = PhpClass::fromFile($classFilePath);
        $trait         = PhpTrait::fromFile($this->getFileName($traitFqn));
        $traits        = $class->getTraits();
        $exists        = array_search($traitFqn, $traits, true);
        if ($exists !== false) {
            return;
        }
        $class->addTrait($trait);
        $this->codeHelper->generate($class, $classFilePath);
    }

    private function getFileName(string $typeFqn): string
    {
        $reflectionClass = new ReflectionClass($typeFqn);

        return $reflectionClass->getFileName();
    }

    public function extendInterfaceWithInterface(string $interfaceToExtendFqn, string $interfaceToAddFqn): void
    {
        $toExtendFilePath = $this->getFileName($interfaceToExtendFqn);
        $toExtend         = PhpInterface::fromFile($toExtendFilePath);
        $toAdd            = PhpInterface::fromFile($this->getFileName($interfaceToAddFqn));
        $exists           = $toExtend->getInterfaces()->contains($interfaceToAddFqn);
        if ($exists !== false) {
            return;
        }
        $toExtend->addInterface($toAdd);
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
}

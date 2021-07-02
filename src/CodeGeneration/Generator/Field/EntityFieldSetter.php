<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
//use gossi\codegen\model\PhpClass;
//use gossi\codegen\model\PhpInterface;
//use gossi\codegen\model\PhpTrait;
use InvalidArgumentException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Factory;
use Symfony\Component\Filesystem\Filesystem;
use ts\Reflection\ReflectionClass;
use function array_keys;
use function class_exists;
use function str_replace;

/**
 * Class EntityFieldSetter
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EntityFieldSetter extends AbstractGenerator
{
    /**
     * @var AbstractTestFakerDataProviderUpdater
     */
    protected $updater;
    /**
     * @var ReflectionHelper
     */
    protected $reflectionHelper;
    /**
     * @var DtoCreator
     */
    private         $dataTransferObjectCreator;
    private Factory $netteFactory;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param Filesystem                           $filesystem
     * @param FileCreationTransaction              $fileCreationTransaction
     * @param NamespaceHelper                      $namespaceHelper
     * @param Config                               $config
     * @param CodeHelper                           $codeHelper
     * @param PathHelper                           $pathHelper
     * @param FindAndReplaceHelper                 $findAndReplaceHelper
     * @param AbstractTestFakerDataProviderUpdater $updater
     * @param ReflectionHelper                     $reflectionHelper
     * @param DtoCreator                           $dataTransferObjectCreator
     */
    public function __construct(
        Filesystem $filesystem,
        FileCreationTransaction $fileCreationTransaction,
        NamespaceHelper $namespaceHelper,
        Config $config,
        CodeHelper $codeHelper,
        PathHelper $pathHelper,
        FindAndReplaceHelper $findAndReplaceHelper,
        AbstractTestFakerDataProviderUpdater $updater,
        ReflectionHelper $reflectionHelper,
        DtoCreator $dataTransferObjectCreator,
        Factory $netteFactory
    ) {
        parent::__construct(
            $filesystem,
            $fileCreationTransaction,
            $namespaceHelper,
            $config,
            $codeHelper,
            $pathHelper,
            $findAndReplaceHelper
        );
        $this->updater                   = $updater;
        $this->reflectionHelper          = $reflectionHelper;
        $this->dataTransferObjectCreator = $dataTransferObjectCreator;
        $this->netteFactory              = $netteFactory;
    }


    /**
     * @param string $fieldFqn
     * @param string $entityFqn
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function setEntityHasField(string $entityFqn, string $fieldFqn): void
    {
        try {
            $entityReflection = new \ReflectionClass($entityFqn);
            $entity           = $this->netteFactory->fromClassReflection($entityReflection, true);
            #$entity                    = PhpClass::fromFile($entityReflection->getFileName());
            $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
            $entityInterfaceReflection = new \ReflectionClass($entityInterfaceFqn);
            $entityInterface           = $this->netteFactory->fromClassReflection($entityInterfaceReflection, true);
            #$entityInterface           = PhpInterface::fromFile($entityInterfaceReflection->getFileName());
            $fieldReflection = new \ReflectionClass($fieldFqn);
            $field           = $this->netteFactory->fromClassReflection($fieldReflection, true);
            #$field                     = PhpTrait::fromFile($fieldReflection->getFileName());
            $fieldInterfaceFqn        = str_replace(
                ['Traits', 'Trait'],
                ['Interfaces', 'Interface'],
                $fieldFqn
            );
            $fieldInterfaceReflection = new \ReflectionClass($fieldInterfaceFqn);
            $this->checkInterfaceLooksLikeField($fieldInterfaceReflection);
            #$fieldInterface = PhpInterface::fromFile($fieldInterfaceReflection->getFileName());
            $fieldInterface = $this->netteFactory->fromClassReflection($fieldInterfaceReflection, true);
        } catch (Exception $exception) {
            throw new DoctrineStaticMetaException(
                'Failed loading the entity or field from FQN: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
        if ($this->alreadyUsingFieldWithThisShortName($entity, $field)) {
            throw new InvalidArgumentException(
                'Entity already has a field with the the short name: ' . $field->getName()
                . "\n\nUse statements:" . print_r($entity->getNamespace()?->getUses() ?? [], true)
                . "\n\nProperty names:" . print_r($entity->getProperties(), true)
            );
        }
        $entity->addTrait($field->getName());
        $this->codeHelper->write($entity, $entityReflection->getFileName());
        $entityInterface->addImplement($fieldInterface->getName());
        $this->codeHelper->write($entityInterface, $entityInterfaceReflection->getFileName());
//@TODO uncomment this block
//        if ($this->fieldHasFakerProvider($fieldReflection)) {
//            $this->updater->updateFakerProviderArrayWithFieldFakerData($this->pathToProjectRoot, $fieldFqn, $entityFqn);
//        }
        $this->dataTransferObjectCreator->setNewObjectFqnFromEntityFqn($entityFqn)
                                        ->setProjectRootDirectory($this->pathToProjectRoot)
                                        ->setProjectRootNamespace($this->projectRootNamespace)
                                        ->createTargetFileObject()
                                        ->write();
    }

    protected function checkInterfaceLooksLikeField(\ReflectionClass $fieldInterfaceReflection): void
    {
        $lookFor = [
            'PROP_',
            'DEFAULT_',
        ];
        $found   = [];
        $consts  = $fieldInterfaceReflection->getConstants();
        foreach (array_keys($consts) as $name) {
            foreach ($lookFor as $key => $prefix) {
                if (\ts\stringStartsWith($name, $prefix)) {
                    $found[$key] = $prefix;
                }
            }
        }
        if ($found !== $lookFor) {
            throw new InvalidArgumentException(
                'Field ' . $fieldInterfaceReflection->getName()
                . ' does not look like a field interface, failed to find the following const prefixes: '
                . "\n" . print_r($lookFor, true)
            );
        }
    }

    /**
     * Using a deprecated method but not sure what alternative there is
     *
     * @TODO ensure test coverage here
     */
    protected function alreadyUsingFieldWithThisShortName(ClassType $entityClass, ClassType $fieldTrait): bool
    {
        $useStatements = $entityClass->getNamespace()?->getUses() ?? [];

        return in_array($fieldTrait->getName(), $useStatements, true);
    }

    protected function fieldHasFakerProvider(ReflectionClass $fieldReflection): bool
    {
        return class_exists(
            $this->reflectionHelper->getFakerProviderFqnFromFieldTraitReflection($fieldReflection)
        );
    }
}

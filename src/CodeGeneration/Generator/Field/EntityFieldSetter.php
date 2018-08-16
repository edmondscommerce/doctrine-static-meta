<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;
use Symfony\Component\Filesystem\Filesystem;

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

    public function __construct(
        Filesystem $filesystem,
        FileCreationTransaction $fileCreationTransaction,
        NamespaceHelper $namespaceHelper,
        Config $config,
        CodeHelper $codeHelper,
        PathHelper $pathHelper,
        FindAndReplaceHelper $findAndReplaceHelper,
        AbstractTestFakerDataProviderUpdater $updater
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
        $this->updater = $updater;
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
            $entityReflection          = new \ts\Reflection\ReflectionClass($entityFqn);
            $entity                    = PhpClass::fromFile($entityReflection->getFileName());
            $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
            $entityInterfaceReflection = new \ts\Reflection\ReflectionClass($entityInterfaceFqn);
            $entityInterface           = PhpInterface::fromFile($entityInterfaceReflection->getFileName());
            $fieldReflection           = new \ts\Reflection\ReflectionClass($fieldFqn);
            $field                     = PhpTrait::fromFile($fieldReflection->getFileName());
            $fieldInterfaceFqn         = \str_replace(
                ['Traits', 'Trait'],
                ['Interfaces', 'Interface'],
                $fieldFqn
            );
            $fieldInterfaceReflection  = new \ts\Reflection\ReflectionClass($fieldInterfaceFqn);
            $this->checkInterfaceLooksLikeField($fieldInterfaceReflection);
            $fieldInterface = PhpInterface::fromFile($fieldInterfaceReflection->getFileName());
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Failed loading the entity or field from FQN: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        if ($this->alreadyUsingFieldWithThisShortName($entity, $field)) {
            throw new \InvalidArgumentException(
                'Entity already has a field with the the short name: ' . $field->getName()
                . "\n\nUse statements:" . print_r($entity->getUseStatements(), true)
                . "\n\nProperty names:" . print_r($entity->getPropertyNames(), true)
            );
        }
        $entity->addTrait($field);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
        $entityInterface->addInterface($fieldInterface);
        $this->codeHelper->generate($entityInterface, $entityInterfaceReflection->getFileName());
        if ($this->fieldHasFakerProvider($fieldReflection)) {
            $this->updater->updateFakerProviderArray($this->pathToProjectRoot, $fieldFqn, $entityFqn);
        }
    }

    protected function alreadyUsingFieldWithThisShortName(PhpClass $entity, PhpTrait $field): bool
    {
        $useStatements = $entity->getUseStatements();

        return null !== $useStatements->get($field->getName());
    }

    protected function fieldHasFakerProvider(\ts\Reflection\ReflectionClass $fieldReflection): bool
    {
        return \class_exists(
            $this->namespaceHelper->getFakerProviderFqnFromFieldTraitReflection($fieldReflection)
        );
    }

    /**
     * @param \ts\Reflection\ReflectionClass $fieldInterfaceReflection
     */
    protected function checkInterfaceLooksLikeField(\ts\Reflection\ReflectionClass $fieldInterfaceReflection): void
    {
        $lookFor = [
            'PROP_',
            'DEFAULT_',
        ];
        $found   = [];
        $consts  = $fieldInterfaceReflection->getConstants();
        foreach (\array_keys($consts) as $name) {
            foreach ($lookFor as $key => $prefix) {
                if (\ts\stringStartsWith($name, $prefix)) {
                    $found[$key] = $prefix;
                }
            }
        }
        if ($found !== $lookFor) {
            throw new \InvalidArgumentException(
                'Field ' . $fieldInterfaceReflection->getName()
                . ' does not look like a field interface, failed to find the following const prefixes: '
                . "\n" . print_r($lookFor, true)
            );
        }
    }
}

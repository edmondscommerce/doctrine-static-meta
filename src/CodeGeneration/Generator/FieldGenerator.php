<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\NameFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\LabelFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person\YearOfBirthFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpTrait;
use gossi\docblock\Docblock;
use gossi\docblock\tags\UnknownTag;

/**
 * Class FieldGenerator
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FieldGenerator extends AbstractGenerator
{
    protected $fieldsPath;

    protected $fieldsInterfacePath;

    protected $classy;

    protected $consty;

    protected $phpType;

    protected $dbalType;

    protected $isNullable = false;

    protected $traitNamespace;

    protected $interfaceNamespace;

    public const STANDARD_FIELDS = [
        NameFieldTrait::class,
        YearOfBirthFieldTrait::class,
        IpAddressFieldTrait::class,
        LabelFieldTrait::class
    ];

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
            $entity           = PhpClass::fromFile($entityReflection->getFileName());
            $fieldReflection  = new \ReflectionClass($fieldFqn);
            $field            = PhpTrait::fromFile($fieldReflection->getFileName());
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Failed loading the entity or field from FQN: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        $entity->addTrait($field);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
    }


    /**
     * Generate a new Field based on a property name and Doctrine Type
     *
     * @see MappingHelper::ALL_TYPES for the full list of Dbal Types
     *
     * @param string $fieldFqn
     * @param string $dbalType
     * @param null|string $phpType
     * @return string - The Fully Qualified Name of the generated Field Trait
     *
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     */
    public function generateField(
        string $fieldFqn,
        string $dbalType,
        ?string $phpType = null
    ): string {
        if (false === strpos($fieldFqn, AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE)) {
            throw new \RuntimeException(
                'Fully qualified name [ ' . $fieldFqn . ' ]'
                . ' does not include [ ' . AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE . ' ].'
                . 'Please ensure you pass in the full namespace qualified field name'
            );
        }

        $this->dbalType   = $dbalType;
        $this->phpType    = $phpType ?? $this->getPhpTypeForDbalType();

        list($className, $traitNamespace, $traitSubDirectories) = $this->parseFullyQualifiedName(
            $fieldFqn,
            $this->srcSubFolderName
        );

        list(, $interfaceNamespace, $interfaceSubDirectories) = $this->parseFullyQualifiedName(
            str_replace('Traits', 'Interfaces', $fieldFqn),
            $this->srcSubFolderName
        );

        $this->fieldsPath = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot . '/' . implode('/', $traitSubDirectories)
        );

        $this->fieldsInterfacePath = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot . '/' . implode('/', $interfaceSubDirectories)
        );

        $this->classy             = Inflector::classify($className);
        $this->consty             = strtoupper(Inflector::tableize($className));
        $this->traitNamespace     = $traitNamespace;
        $this->interfaceNamespace = $interfaceNamespace;

        $this->ensurePathExists($this->fieldsPath);
        $this->ensurePathExists($this->fieldsInterfacePath);

        $this->generateInterface();
        return $this->generateTrait();
    }

    /**
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    protected function ensurePathExists($path)
    {
        if ($this->fileSystem->exists($path)) {
            return;
        }
        $this->fileSystem->mkdir($path);
    }


    /**
     * @return string
     * @throws DoctrineStaticMetaException
     *
     */
    protected function getPhpTypeForDbalType(): string
    {
        if (!\in_array($this->dbalType, MappingHelper::COMMON_TYPES, true)) {
            throw new DoctrineStaticMetaException(
                'Invalid field type of '.$this->dbalType.', must be one of MappingHelper::COMMON_TYPES'
                ."\n\nNote - if you want to use another type, suggest creating with `string` "
                .'and then you can edit the generated code as you see fit'
            );
        }

        return MappingHelper::COMMON_TYPES_TO_PHP_TYPES[$this->dbalType];
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function generateInterface()
    {
        $filePath = $this->fieldsInterfacePath . '/' . $this->classy.'FieldInterface.php';
        try {
            $this->fileSystem->copy(
                $this->codeHelper->resolvePath(static::FIELD_INTERFACE_TEMPLATE_PATH),
                $filePath
            );
            $this->interfacePostCopy($filePath);
            $this->codeHelper->replaceTypeHintsInFile(
                $filePath,
                $this->phpType,
                $this->isNullable
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Error in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $filePath
     *
     * @throws \RuntimeException
     */
    protected function postCopy(string $filePath)
    {
        $this->fileCreationTransaction::setPathCreated($filePath);
        $this->replaceName(
            $this->classy,
            $filePath,
            static::FIND_ENTITY_FIELD_NAME
        );
        $this->findReplace('TEMPLATE_FIELD_NAME', $this->consty, $filePath);
        $this->codeHelper->tidyNamespacesInFile($filePath);
    }

    /**
     * @param string $filePath
     * @throws \RuntimeException
     */
    protected function traitPostCopy(string $filePath)
    {
        $this->replaceFieldTraitNamespace($this->traitNamespace, $filePath);
        $this->replaceFieldInterfaceNamespace($this->interfaceNamespace, $filePath);
        $this->postCopy($filePath);
    }

    /**
     * @param string $filePath
     * @throws \RuntimeException
     */
    protected function interfacePostCopy(string $filePath)
    {
        $this->replaceFieldInterfaceNamespace($this->interfaceNamespace, $filePath);
        $this->postCopy($filePath);
    }

    /**
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function generateTrait(): string
    {
        $filePath = $this->fieldsPath . '/' . $this->classy.'FieldTrait.php';
        try {
            $this->fileSystem->copy(
                $this->codeHelper->resolvePath(static::FIELD_TRAIT_TEMPLATE_PATH),
                $filePath
            );
            $this->fileCreationTransaction::setPathCreated($filePath);
            $this->traitPostCopy($filePath);
            $trait = PhpTrait::fromFile($filePath);
            $trait->setMethod($this->getPropertyMetaMethod());
            $trait->addUseStatement('\\'.MappingHelper::class);
            $trait->addUseStatement('\\'.ClassMetadataBuilder::class);
            $this->codeHelper->generate($trait, $filePath);
            $this->codeHelper->replaceTypeHintsInFile(
                $filePath,
                $this->phpType,
                $this->isNullable
            );

            return $trait->getQualifiedName();
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Error in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     *
     * @return PhpMethod
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getPropertyMetaMethod(): PhpMethod
    {
        $name   = UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META.$this->classy;
        $method = PhpMethod::create($name);
        $method->setStatic(true);
        $method->setVisibility('public');
        $method->setParameters(
            [PhpParameter::create('builder')->setType('ClassMetadataBuilder')]
        );
        $mappingHelperMethodName = 'setSimple'.ucfirst(strtolower($this->dbalType)).'Fields';
        $isNullableString = $this->isNullable ? 'true' : 'false';
        $method->setBody(
            "
        MappingHelper::$mappingHelperMethodName(
            [{$this->classy}FieldInterface::PROP_{$this->consty}],
            \$builder,
            $isNullableString
        );                        
"
        );
        $method->setDocblock(
            DocBlock::create()
                    ->appendTag(
                        UnknownTag::create("SuppressWarnings(PHPMD.StaticAccess)")
                    )
        );

        return $method;
    }

    public function setIsNullable(bool $isNullable): FieldGenerator
    {
        $this->isNullable = $isNullable;
        return $this;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }
}

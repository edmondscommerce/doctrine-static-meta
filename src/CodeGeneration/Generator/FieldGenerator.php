<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\LabelFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\NameFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\QtyFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\ActionedDateFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\ActivatedDateFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\CompletedDateFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\DeactivatedDateFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date\TimestampFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag\ApprovedFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag\DefaultFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person\EmailFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person\YearOfBirthFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
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
        // Attribute
        IpAddressFieldTrait::class,
        LabelFieldTrait::class,
        NameFieldTrait::class,
        QtyFieldTrait::class,
        // Date
        ActionedDateFieldTrait::class,
        ActivatedDateFieldTrait::class,
        CompletedDateFieldTrait::class,
        DeactivatedDateFieldTrait::class,
        TimestampFieldTrait::class,
        // Flag
        ApprovedFieldTrait::class,
        DefaultFieldTrait::class,
        // Person
        EmailFieldTrait::class,
        YearOfBirthFieldTrait::class,
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
            $entityReflection         = new \ReflectionClass($entityFqn);
            $entity                   = PhpClass::fromFile($entityReflection->getFileName());
            $fieldReflection          = new \ReflectionClass($fieldFqn);
            $field                    = PhpTrait::fromFile($fieldReflection->getFileName());
            $fieldInterfaceFqn        = \str_replace(
                ['Traits', 'Trait'],
                ['Interfaces', 'Interface'],
                $fieldFqn
            );
            $fieldInterfaceReflection = new \ReflectionClass($fieldInterfaceFqn);
            $fieldInterface           = PhpInterface::fromFile($fieldInterfaceReflection->getFileName());
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Failed loading the entity or field from FQN: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        $entity->addTrait($field);
        $entity->addInterface($fieldInterface);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
    }


    /**
     * Generate a new Field based on a property name and Doctrine Type
     *
     * @see MappingHelper::ALL_DBAL_TYPES for the full list of Dbal Types
     *
     * @param string      $fieldFqn
     * @param string      $dbalType
     * @param null|string $phpType
     *
     * @return string - The Fully Qualified Name of the generated Field Trait
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \InvalidArgumentException
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
                'Fully qualified name [ '.$fieldFqn.' ]'
                .' does not include [ '.AbstractGenerator::ENTITY_FIELD_TRAIT_NAMESPACE.' ].'."\n"
                .'Please ensure you pass in the full namespace qualified field name'
            );
        }
        if (false === \in_array($dbalType, MappingHelper::ALL_DBAL_TYPES, true)) {
            throw new \InvalidArgumentException(
                'dbalType must be either null or one of MappingHelper::ALL_DBAL_TYPES'
            );
        }
        if (
            (null !== $phpType)
            && (false === \in_array($phpType, MappingHelper::PHP_TYPES, true))
        ) {
            throw new \InvalidArgumentException(
                'phpType must be either null or one of MappingHelper::PHP_TYPES'
            );
        }

        $this->dbalType = $dbalType;
        $this->phpType  = $phpType ?? $this->getPhpTypeForDbalType();

        list($className, $traitNamespace, $traitSubDirectories) = $this->parseFullyQualifiedName(
            $fieldFqn,
            $this->srcSubFolderName
        );

        list(, $interfaceNamespace, $interfaceSubDirectories) = $this->parseFullyQualifiedName(
            str_replace('Traits', 'Interfaces', $fieldFqn),
            $this->srcSubFolderName
        );

        $this->fieldsPath = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot.'/'.implode('/', $traitSubDirectories)
        );

        $this->fieldsInterfacePath = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot.'/'.implode('/', $interfaceSubDirectories)
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
     * @param $path
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    protected function ensurePathExists($path): void
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
                'Field type of '.$this->dbalType.' is not one of MappingHelper::COMMON_TYPES'
                ."\n\nYou can only use this dbal type if you pass in the explicit phpType as well "
                ."\n\nAlternatively, suggest you set the type as string and then edit the generated code as you see fit"
            );
        }

        return MappingHelper::COMMON_TYPES_TO_PHP_TYPES[$this->dbalType];
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function generateInterface(): void
    {
        $filePath = $this->fieldsInterfacePath.'/'.$this->classy.'FieldInterface.php';
        try {
            $this->fileSystem->copy(
                $this->codeHelper->resolvePath(static::FIELD_INTERFACE_TEMPLATE_PATH),
                $filePath
            );
            $this->interfacePostCopy($filePath);
            $this->codeHelper->replaceTypeHintsInFile(
                $filePath,
                $this->phpType,
                $this->dbalType,
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
     * @throws DoctrineStaticMetaException
     */
    protected function postCopy(string $filePath): void
    {
        $this->fileCreationTransaction::setPathCreated($filePath);
        $this->replaceName(
            $this->classy,
            $filePath,
            static::FIND_ENTITY_FIELD_NAME
        );
        $this->findReplace('TEMPLATE_FIELD_NAME', $this->consty, $filePath);
        $this->codeHelper->tidyNamespacesInFile($filePath);
        $this->setGetterToIsForBools($filePath);
    }

    /**
     * @param string $filePath
     *
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    protected function traitPostCopy(string $filePath): void
    {
        $this->replaceFieldTraitNamespace($this->traitNamespace, $filePath);
        $this->replaceFieldInterfaceNamespace($this->interfaceNamespace, $filePath);
        $this->postCopy($filePath);
    }

    protected function setGetterToIsForBools(string $filePath): void
    {
        if ($this->phpType !== 'bool') {
            return;
        }
        $this->findReplace(' get', ' is', $filePath);
    }

    /**
     * @param string $filePath
     *
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    protected function interfacePostCopy(string $filePath): void
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
        $filePath = $this->fieldsPath.'/'.$this->classy.'FieldTrait.php';
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
                $this->dbalType,
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
        $isNullableString        = $this->isNullable ? 'true' : 'false';
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
                        UnknownTag::create('SuppressWarnings(PHPMD.StaticAccess)')
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

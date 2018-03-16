<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\IpAddressFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\LabelFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person\NameFieldTrait;
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

    protected $classy;

    protected $consty;

    protected $phpType;

    protected $dbalType;

    protected $isNullable = false;

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
     * @param string      $propertyName
     * @param string      $dbalType
     * @param null|string $phpType
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @return string - The Fully Qualified Name of the generated Field Trait
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    public function generateField(
        string $propertyName,
        string $dbalType,
        ?string $phpType = null
    ): string {
        $this->dbalType   = $dbalType;
        $this->phpType    = $phpType ?? $this->getPhpTypeForDbalType();
        $this->fieldsPath = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot.'/src/'.self::ENTITY_FIELDS_FOLDER_NAME
        );
        $this->classy     = Inflector::classify($propertyName);
        $this->consty     = strtoupper(Inflector::tableize($propertyName));
        $this->ensureFieldsPathExists();
        $this->generateInterface();

        return $this->generateTrait();
    }

    /**
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    protected function ensureFieldsPathExists()
    {
        if ($this->fileSystem->exists($this->fieldsPath)) {
            return;
        }
        $this->fileSystem->mkdir($this->fieldsPath);
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
        $filePath = $this->fieldsPath.'/Interfaces/'.$this->classy.'FieldInterface.php';
        try {
            $this->fileSystem->copy(
                $this->codeHelper->resolvePath(static::FIELD_INTERFACE_TEMPLATE_PATH),
                $filePath
            );
            $this->postCopy($filePath);
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
    protected function postCopy(string $filePath)
    {
        $this->fileCreationTransaction::setPathCreated($filePath);
        $this->replaceName(
            $this->classy,
            $filePath,
            static::FIND_ENTITY_FIELD_NAME
        );
        $this->replaceProjectNamespace($this->projectRootNamespace, $filePath);
        $this->findReplace('TEMPLATE_FIELD_NAME', $this->consty, $filePath);
        $this->codeHelper->replaceTypeHintsInFile($filePath, $this->phpType, $this->isNullable);
        $this->codeHelper->tidyNamespacesInFile($filePath);
    }

    /**
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function generateTrait(): string
    {
        $filePath = $this->fieldsPath.'/Traits/'.$this->classy.'FieldTrait.php';
        try {
            $this->fileSystem->copy(
                $this->codeHelper->resolvePath(static::FIELD_TRAIT_TEMPLATE_PATH),
                $filePath
            );
            $this->fileCreationTransaction::setPathCreated($filePath);
            $this->postCopy($filePath);
            $trait = PhpTrait::fromFile($filePath);
            $trait->setMethod($this->getPropertyMetaMethod());
            $trait->addUseStatement('\\'.MappingHelper::class);
            $trait->addUseStatement('\\'.ClassMetadataBuilder::class);
            $this->codeHelper->generate($trait, $filePath);

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

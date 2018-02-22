<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
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
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    public function generateField(
        string $propertyName,
        string $dbalType,
        ?string $phpType = null
    ) {
        $this->dbalType   = $dbalType;
        $this->phpType    = $phpType ?? $this->getPhpTypeForDbalType();
        $this->fieldsPath = $this->codeHelper->resolvePath(
            $this->pathToProjectRoot.'/src/'.self::ENTITY_FIELDS_FOLDER_NAME
        );
        $this->classy     = Inflector::classify($propertyName);
        $this->consty     = strtoupper(Inflector::tableize($propertyName));
        $this->ensureFieldsPathExists();
        $this->generateInterface();
        $this->generateTrait();
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
        $this->codeHelper->replaceTypeHints($filePath, $this->phpType);
    }

    /**
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function generateTrait()
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
            $trait->addUseStatement(
                $this->projectRootNamespace.AbstractGenerator::ENTITY_FIELD_NAMESPACE
                .'\\Interfaces\\'.$this->classy.'Interface'
            );
            $this->codeHelper->generate($trait, $filePath);
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
            [PhpParameter::create('builder')->setType('\\'.ClassMetadataBuilder::class)]
        );
        $mappingHelperMethodName = 'setSimple'.ucfirst(strtolower($this->dbalType)).'Fields';
        $method->setBody(
            "
        MappingHelper::$mappingHelperMethodName(
            [{$this->classy}Interface::PROP_{$this->consty}],
            \$builder
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

}

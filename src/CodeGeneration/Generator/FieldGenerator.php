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

class FieldGenerator extends AbstractGenerator
{
    protected $fieldsPath;

    /**
     * @param string      $propertyName
     * @param string      $dbalType
     * @param null|string $phpType
     *
     * @throws DoctrineStaticMetaException
     */
    public function generateField(
        string $propertyName,
        string $dbalType,
        ?string $phpType = null
    ) {
        $phpType          = $phpType ?? $this->getPhpTypeForDbalType($dbalType);
        $this->fieldsPath = $this->resolvePath(
            $this->pathToProjectRoot.'/src/'.self::ENTITY_FIELDS_FOLDER_NAME
        );
        $this->ensureFieldsPathExists();
        $this->generateInterface($propertyName, $phpType);
        $this->generateTrait($propertyName, $phpType, $dbalType);
    }

    protected function ensureFieldsPathExists()
    {
        if ($this->fileSystem->exists($this->fieldsPath)) {
            return;
        }
        $this->fileSystem->mkdir($this->fieldsPath);
    }


    /**
     * @param string $dbalType
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getPhpTypeForDbalType(string $dbalType): string
    {
        if (!\in_array($dbalType, MappingHelper::COMMON_TYPES, true)) {
            throw new DoctrineStaticMetaException(
                'Invalid field type of '.$dbalType.', must be one of MappingHelper::COMMON_TYPES'
                ."\n\nNote - if you want to use another type, suggest creating with `string` "
                .'and then you can edit the generated code as you see fit'
            );
        }

        return MappingHelper::COMMON_TYPES_TO_PHP_TYPES[$dbalType];
    }

    /**
     * @param string $propertyName
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function generateInterface(string $propertyName, string $phpType)
    {
        $filePath = $this->fieldsPath.'/Interfaces/'.Inflector::classify($propertyName).'FieldInterface.php';
        try {
            $this->fileSystem->copy(
                $this->resolvePath(static::FIELD_INTERFACE_TEMPLATE_PATH),
                $filePath
            );
            $this->fileCreationTransaction::setPathCreated($filePath);
            $this->replaceName(
                Inflector::classify($propertyName),
                $filePath,
                static::FIND_ENTITY_FIELD_NAME
            );
            $this->replaceTypeHints($filePath, $phpType);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Error in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $propertyName
     * @param string $phpType
     * @param string $dbalType
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function generateTrait(string $propertyName, string $phpType, string $dbalType)
    {
        $filePath = $this->fieldsPath.'/Traits/'.Inflector::classify($propertyName).'FieldTrait.php';
        try {
            $this->fileSystem->copy(
                $this->resolvePath(static::FIELD_TRAIT_TEMPLATE_PATH),
                $filePath
            );
            $this->fileCreationTransaction::setPathCreated($filePath);
            $this->replaceName(
                Inflector::classify($propertyName),
                $filePath,
                static::FIND_ENTITY_FIELD_NAME
            );
            $this->replaceTypeHints($filePath, $phpType);
            $trait = PhpTrait::fromFile($filePath);
            $trait->setMethod($this->getPropertyMetaMethod($propertyName, $dbalType));
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Error in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $propertyName
     * @param string $dbalType
     *
     * @return PhpMethod
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getPropertyMetaMethod(string $propertyName, string $dbalType): PhpMethod
    {
        $name   = UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META.Inflector::classify($propertyName);
        $method = PhpMethod::create($name);
        $method->setStatic(true);
        $method->setVisibility('public');
        $method->setParameters(
            [PhpParameter::create('builder')->setType('\\'.ClassMetadataBuilder::class)]
        );
        $mappingHelperMethodName = 'setSimple'.ucfirst(strtolower($dbalType)).'Fields';
        $method->setBody(
            "
        MappingHelper::$mappingHelperMethodName(
            [TemplateNameInterface::PROP_TEMPLATE_NAME],
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

    protected function replaceTypeHints(string $path, $type)
    {
        $contents = file_get_contents($path);
        //argument hints
        $contents = preg_replace(
            '%\( string \$%',
            '( '.$type.' $',
            $contents
        );
        //return hints
        $contents = preg_replace(
            '%: string(\s+?[{;])%',
            ': '.$type.'$1',
            $contents
        );
        file_put_contents($path, $contents);
    }
}

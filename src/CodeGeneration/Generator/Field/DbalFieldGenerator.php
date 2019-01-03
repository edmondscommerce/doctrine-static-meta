<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpTrait;
use gossi\docblock\Docblock;
use gossi\docblock\tags\UnknownTag;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DbalFieldGenerator
 *
 * @package  EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @internal - this is only accessed via CodeGeneration\Generator\Field\FieldGenerator
 */
class DbalFieldGenerator
{
    /**
     * @var string
     */
    protected $traitPath;
    /**
     * @var string
     */
    protected $interfacePath;
    /**
     * @var null|string
     */
    protected $phpType;
    /**
     * @var null
     */
    protected $defaultValue;
    /**
     * @var bool
     */
    protected $isUnique;
    /**
     * @var bool
     */
    protected $isNullable;
    /**
     * @var string
     */
    protected $dbalType;
    /**
     * @var Filesystem
     */
    protected $fileSystem;
    /**
     * @var CodeHelper
     */
    protected $codeHelper;
    /**
     * @var FileCreationTransaction
     */
    protected $fileCreationTransaction;
    /**
     * @var FindAndReplaceHelper
     */
    protected $findAndReplaceHelper;
    /**
     * @var string
     */
    protected $className;
    /**
     * @var TypeHelper
     */
    protected $typeHelper;
    /**
     * @var string
     */
    protected $traitNamespace;
    /**
     * @var string
     */
    protected $interfaceNamespace;
    /**
     * @var PathHelper
     */
    protected $pathHelper;

    public function __construct(
        Filesystem $fileSystem,
        CodeHelper $codeHelper,
        FileCreationTransaction $fileCreationTransaction,
        FindAndReplaceHelper $findAndReplaceHelper,
        TypeHelper $typeHelper,
        PathHelper $pathHelper
    ) {
        $this->fileSystem              = $fileSystem;
        $this->codeHelper              = $codeHelper;
        $this->fileCreationTransaction = $fileCreationTransaction;
        $this->findAndReplaceHelper    = $findAndReplaceHelper;
        $this->typeHelper              = $typeHelper;
        $this->pathHelper              = $pathHelper;
    }

    /**
     * @param string      $className
     * @param string      $traitPath
     * @param string      $interfacePath
     * @param string      $dbalType
     * @param null        $defaultValue
     * @param bool        $isUnique
     * @param null|string $phpType
     *
     * @param string      $traitNamespace
     * @param string      $interfaceNamespace
     *
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function create(
        string $className,
        string $traitPath,
        string $interfacePath,
        string $dbalType,
        $defaultValue = null,
        bool $isUnique = false,
        ?string $phpType = null,
        string $traitNamespace,
        string $interfaceNamespace
    ): string {
        $this->traitPath          = $traitPath;
        $this->interfacePath      = $interfacePath;
        $this->phpType            = $phpType;
        $this->defaultValue       = $defaultValue;
        $this->isUnique           = $isUnique;
        $this->isNullable         = (null === $defaultValue);
        $this->dbalType           = $dbalType;
        $this->className          = $className;
        $this->traitNamespace     = $traitNamespace;
        $this->interfaceNamespace = $interfaceNamespace;
        $this->generateInterface();

        return $this->generateTrait();
    }

    /**
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function generateInterface(): void
    {
        try {
            $this->fileSystem->copy(
                $this->pathHelper->resolvePath(FieldGenerator::FIELD_INTERFACE_TEMPLATE_PATH),
                $this->interfacePath
            );
            $this->interfacePostCopy($this->interfacePath);
            $this->codeHelper->replaceTypeHintsInFile(
                $this->interfacePath,
                $this->phpType,
                $this->dbalType,
                $this->isNullable
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Error in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $filePath
     *
     * @throws DoctrineStaticMetaException
     */
    protected function interfacePostCopy(
        string $filePath
    ): void {
        $this->findAndReplaceHelper->replaceFieldInterfaceNamespace($this->interfaceNamespace, $filePath);
        $this->replaceDefaultValueInInterface($filePath);
        $this->postCopy($filePath);
    }

    /**
     * @param string $filePath
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function replaceDefaultValueInInterface(
        string $filePath
    ): void {
        $defaultType = $this->typeHelper->getType($this->defaultValue);
        switch (true) {
            case $defaultType === 'null':
                $replace = 'null';
                break;
            case $this->phpType === 'string':
                $replace = "'$this->defaultValue'";
                break;
            case $this->phpType === 'bool':
                $replace = true === $this->defaultValue ? 'true' : 'false';
                break;
            case $this->phpType === 'float':
                $replace = (string)$this->defaultValue;
                if (false === \ts\stringContains($replace, '.')) {
                    $replace .= '.0';
                }
                break;
            case $this->phpType === 'int':
                $replace = (string)$this->defaultValue;
                break;
            case $this->phpType === trim(MappingHelper::PHP_TYPE_DATETIME, '\\'):
                if ($this->defaultValue !== null) {
                    throw new \InvalidArgumentException(
                        'Invalid default value ' . $this->defaultValue
                        . 'Currently we only support null as a default for DateTime'
                    );
                }
                $replace = 'null';
                break;
            default:
                throw new \RuntimeException(
                    'failed to calculate replace based on defaultType ' . $defaultType
                    . ' and phpType ' . $this->phpType . ' in ' . __METHOD__
                );
        }
        $this->findAndReplaceHelper->findReplace("'defaultValue'", $replace, $filePath);
    }

    /**
     * @param string $filePath
     *
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    protected function postCopy(
        string $filePath
    ): void {
        $this->fileCreationTransaction::setPathCreated($filePath);
        $this->findAndReplaceHelper->replaceName(
            $this->codeHelper->classy($this->className),
            $filePath,
            FieldGenerator::FIND_ENTITY_FIELD_NAME
        );
        $this->findAndReplaceHelper->findReplace(
            $this->codeHelper->consty(FieldGenerator::FIND_ENTITY_FIELD_NAME),
            $this->codeHelper->consty($this->className),
            $filePath
        );
        $this->codeHelper->tidyNamespacesInFile($filePath);
        $this->setGetterToIsForBools($filePath);
    }

    protected function setGetterToIsForBools(
        string $filePath
    ): void {
        if ($this->phpType !== 'bool') {
            return;
        }
        $replaceName = $this->codeHelper->getGetterMethodNameForBoolean($this->codeHelper->classy($this->className));
        $findName    = 'get' . $this->codeHelper->classy($this->className);
        $this->findAndReplaceHelper->findReplace($findName, $replaceName, $filePath);
    }

    /**
     * @return string
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function generateTrait(): string
    {
        try {
            $this->fileSystem->copy(
                $this->pathHelper->resolvePath(FieldGenerator::FIELD_TRAIT_TEMPLATE_PATH),
                $this->traitPath
            );
            $this->fileCreationTransaction::setPathCreated($this->traitPath);
            $this->traitPostCopy($this->traitPath);
            $trait = PhpTrait::fromFile($this->traitPath);
            $trait->setMethod($this->getPropertyMetaMethod());
            $trait->addUseStatement('\\' . MappingHelper::class);
            $trait->addUseStatement('\\' . ClassMetadataBuilder::class);
            $this->codeHelper->generate($trait, $this->traitPath);
            $this->codeHelper->replaceTypeHintsInFile(
                $this->traitPath,
                $this->phpType,
                $this->dbalType,
                $this->isNullable
            );
            $this->breakUpdateCallOntoMultipleLines();

            return $trait->getQualifiedName();
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Error in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $filePath
     *
     * @throws \RuntimeException
     * @throws DoctrineStaticMetaException
     */
    protected function traitPostCopy(
        string $filePath
    ): void {
        $this->findAndReplaceHelper->replaceFieldTraitNamespace($this->traitNamespace, $filePath);
        $this->findAndReplaceHelper->replaceFieldInterfaceNamespace($this->interfaceNamespace, $filePath);
        $this->postCopy($filePath);
    }

    /**
     * @return PhpMethod
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function getPropertyMetaMethod(): PhpMethod
    {
        $classy = $this->codeHelper->classy($this->className);
        $consty = $this->codeHelper->consty($this->className);
        $name   = UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META . $classy;
        $method = PhpMethod::create($name);
        $method->setStatic(true);
        $method->setVisibility('public');
        $method->setParameters(
            [PhpParameter::create('builder')->setType('ClassMetadataBuilder')]
        );
        $mappingHelperMethodName = 'setSimple' . ucfirst(strtolower($this->dbalType)) . 'Fields';

        $methodBody = "
        MappingHelper::$mappingHelperMethodName(
            [{$classy}FieldInterface::PROP_{$consty}],
            \$builder,
            {$classy}FieldInterface::DEFAULT_{$consty}
        );                        
";
        if (\in_array($this->dbalType, MappingHelper::UNIQUEABLE_TYPES, true)) {
            $isUniqueString = $this->isUnique ? 'true' : 'false';
            $methodBody     = "
        MappingHelper::$mappingHelperMethodName(
            [{$classy}FieldInterface::PROP_{$consty}],
            \$builder,
            {$classy}FieldInterface::DEFAULT_{$consty},
            $isUniqueString
        );                        
";
        }
        $method->setBody($methodBody);
        $method->setDocblock(
            Docblock::create()
                    ->appendTag(
                        UnknownTag::create('SuppressWarnings(PHPMD.StaticAccess)')
                    )
        );

        return $method;
    }

    private function breakUpdateCallOntoMultipleLines(): void
    {
        $contents = \ts\file_get_contents($this->traitPath);
        $indent   = '            ';
        $updated  = \preg_replace(
            [
                '%updatePropertyValue\((.+?),(.+?)\)%',
            ],
            [
                "updatePropertyValue(\n$indent\$1,\n$indent\$2\n        )",
            ],
            $contents
        );
        \file_put_contents($this->traitPath, $updated);
    }

    /**
     * @return PhpMethod
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    protected function getPropertyMetaMethodForDatetime(): PhpMethod
    {
        $classy = $this->codeHelper->classy($this->className);
        $consty = $this->codeHelper->consty($this->className);
        $name   = UsesPHPMetaDataInterface::METHOD_PREFIX_GET_PROPERTY_DOCTRINE_META . $classy;
        $method = PhpMethod::create($name);
        $method->setStatic(true);
        $method->setVisibility('public');
        $method->setParameters(
            [PhpParameter::create('builder')->setType('ClassMetadataBuilder')]
        );
        $mappingHelperMethodName = 'setSimple' . ucfirst(strtolower($this->dbalType)) . 'Fields';

        $methodBody = "
        MappingHelper::$mappingHelperMethodName(
            [{$classy}FieldInterface::PROP_{$consty}],
            \$builder,
            {$classy}FieldInterface::DEFAULT_{$consty}
        );                        
";
        if (\in_array($this->dbalType, MappingHelper::UNIQUEABLE_TYPES, true)) {
            $isUniqueString = $this->isUnique ? 'true' : 'false';
            $methodBody     = "
        MappingHelper::$mappingHelperMethodName(
            [{$classy}FieldInterface::PROP_{$consty}],
            \$builder,
            {$classy}FieldInterface::DEFAULT_{$consty},
            $isUniqueString
        );                        
";
        }
        $method->setBody($methodBody);
        $method->setDocblock(
            Docblock::create()
                    ->appendTag(
                        UnknownTag::create('SuppressWarnings(PHPMD.StaticAccess)')
                    )
        );

        return $method;
    }
}

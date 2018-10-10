<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;

class CreateDtoBodyProcess implements ProcessInterface
{
    /**
     * @var DoctrineStaticMeta
     */
    private $dsm;
    /**
     * @var ReflectionHelper
     */
    private $reflectionHelper;

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var array
     */
    private $setters = [];

    /**
     * @var array
     */
    private $getters = [];
    /**
     * @var CodeHelper
     */
    private $codeHelper;
    /**
     * @var string
     */
    private $entityFqn;
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function __construct(
        ReflectionHelper $reflectionHelper,
        CodeHelper $codeHelper,
        NamespaceHelper $namespaceHelper
    ) {
        $this->reflectionHelper = $reflectionHelper;
        $this->codeHelper       = $codeHelper;
        $this->namespaceHelper  = $namespaceHelper;
    }

    public function setEntityFqn(string $entityFqn)
    {
        $this->entityFqn = $entityFqn;
        if (false === \ts\stringContains($entityFqn, '\\Entities\\')) {
            throw new \RuntimeException(
                'This does not look like an Entity FQN: ' . $entityFqn
            );
        }
        $this->dsm = new DoctrineStaticMeta($entityFqn);

        return $this;
    }

    public function run(File\FindReplace $findReplace): void
    {
        $this->buildArraysOfCode();
        $this->updateFileContents($findReplace);
    }

    private function buildArraysOfCode()
    {
        foreach ($this->dsm->getSetters() as $getterName => $setterName) {
            $trait    = $this->reflectionHelper->getTraitImplementingMethod(
                $this->dsm->getReflectionClass(),
                $setterName
            );
            $setter   = $trait->getMethod($setterName);
            $property = $trait->getProperties(\ReflectionProperty::IS_PRIVATE)[0]->getName();
            $type     = $this->getPropertyTypeFromSetter($setter);
            $this->setProperty($property, $type);
            $this->setGetterFromPropertyAndType($getterName, $property, $type);
            $this->setSetterFromPropertyAndType($setterName, $property, $type);
        }
    }

    private function getPropertyTypeFromSetter(\ReflectionMethod $setter): string
    {
        /**
         * @var \ReflectionParameter $param
         */
        $param = current($setter->getParameters());
        $type  = $param->getType();
        if (null !== $type) {
            $type = $type->getName();
            if (!\in_array($type, ['string', 'bool', 'int', 'float'], true)) {
                $type = "\\$type";
            }
            if ($param->allowsNull()) {
                $type = "?$type";
            }
        }

        return (string)$type;
    }

    private function setProperty(string $property, string $type): void
    {
        $defaultValue       = $this->getDefaultValueCodeForProperty($property);
        $code               = '';
        $code               .= "\n" . '    /**';
        $code               .= ('' !== $type) ? "\n" . '     * @var ' . $type : '';
        $code               .= "\n" . '     */';
        $code               .= "\n" . '    private $' . $property . ' = ' . $defaultValue . ';';
        $this->properties[] = $code;
    }

    private function getDefaultValueCodeForProperty(string $property)
    {
        $defaultValueConst = 'DEFAULT_' . $this->codeHelper->consty($property);
        $fullValueString   = $this->entityFqn . '::' . $defaultValueConst;
        if (\defined($fullValueString)) {
            return $this->dsm->getShortName() . '::' . $defaultValueConst;
        }

        return 'null';
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function setGetterFromPropertyAndType(string $getterName, string $property, string $type)
    {
        $code = '';
        $code .= "\n    public function $getterName()" . (('' !== $type) ? ": $type" : '');
        $code .= "\n    {";
        if ('\Doctrine\Common\Collections\Collection' === $type) {
            $code .= "\n        return \$this->$property ?? \$this->$property = new ArrayCollection();";
        } else {
            $code .= "\n        return \$this->$property;";
        }
        $code            .= "\n    }\n";
        $this->getters[] = $code;
    }

    private function setSetterFromPropertyAndType(string $setterName, string $property, string $type)
    {
        $code            = '';
        $code            .= "\n    public function $setterName($type \$$property): self ";
        $code            .= "\n    {";
        $code            .= "\n        \$this->$property = \$$property;";
        $code            .= "\n        return \$this;";
        $code            .= "\n    }\n";
        $this->setters[] = $code;
        if (\ts\stringContains($type, '\\Entity\\Interfaces\\')) {
            $this->setDtoGetterAndSetterForEntityProperty($setterName, $property, $type);
        }
    }

    private function setDtoGetterAndSetterForEntityProperty(
        string $setterName,
        string $property,
        string $entityInterfaceFqn
    ) {
        $dtoFqn          = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn(
            $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($entityInterfaceFqn)
        );
        $setterCode      = '';
        $setterCode      .= "\n    public function ${setterName}Dto($dtoFqn \$$property): self ";
        $setterCode      .= "\n    {";
        $setterCode      .= "\n        \$this->$property = \$$property;";
        $setterCode      .= "\n        return \$this;";
        $setterCode      .= "\n    }\n";
        $this->setters[] = $setterCode;

        $getterName      = 'get' . substr($setterName, 3);
        $getterCode      = '';
        $getterCode      .= "\n    public function ${getterName}Dto(): $dtoFqn";
        $getterCode      .= "\n    {";
        $getterCode      .= "\n        return \$this->$property;";
        $getterCode      .= "\n    }\n";
        $this->getters[] = $getterCode;
    }

    private function updateFileContents(File\FindReplace $findReplace)
    {
        sort($this->properties, SORT_STRING);
        sort($this->getters, SORT_STRING);
        sort($this->setters, SORT_STRING);

        $body = implode("\n", $this->properties) .
                "\n\n" .
                implode("\n", $this->getters) .
                "\n" .
                implode("\n", $this->setters);

        $findReplace->findReplaceRegex('%{(.+)}%s', "{\n\$1\n$body\n}");
    }
}

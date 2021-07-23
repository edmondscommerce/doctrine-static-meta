<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity\DataTransferObjects;

use Doctrine\Common\Collections\Collection;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use ReflectionParameter;
use RuntimeException;
use ts\Reflection\ReflectionClass;
use ts\Reflection\ReflectionMethod;
use function defined;
use function in_array;

class CreateDtoBodyProcess implements ProcessInterface
{
    /** @var string[] */
    private array $properties = [];

    /** @var string[] */
    private array $setters = [];

    /** @var string[] */
    private array $getters = [];

    /** @var string[] */
    private array $imports = [];
    /**
     * @var string
     */
    private                    $entityFqn;
    private DoctrineStaticMeta $dsm;

    public function __construct(
        private ReflectionHelper $reflectionHelper,
        private CodeHelper $codeHelper,
        private NamespaceHelper $namespaceHelper,
        private TypeHelper $typeHelper
    ) {
    }

    public function setEntityFqn(string $entityFqn)
    {
        $this->entityFqn = $entityFqn;
        if (false === \ts\stringContains($entityFqn, '\\Entities\\')) {
            throw new RuntimeException(
                'This does not look like an Entity FQN: ' . $entityFqn
            );
        }
        $this->dsm = new DoctrineStaticMeta($entityFqn);

        return $this;
    }

    public function run(File\FindReplace $findReplace): void
    {
        $this->setImports($findReplace);
        $this->buildArraysOfCode();
        $this->updateFileContents($findReplace);
    }

    private function setImports(File\FindReplace $findReplace): void
    {
        $this->imports = array_map('trim', $findReplace->findAll("%^use.+?;%m"));
    }

    private function buildArraysOfCode(): void
    {
        foreach ($this->dsm->getSetters() as $getterName => $setterName) {
            if ('getId' === $getterName) {
                continue;
            }
            $reflectionClassWithMethod = $this->reflectionHelper->getTraitImplementingMethod(
                $this->dsm->getReflectionClass(),
                $setterName
            );
            if (null === $reflectionClassWithMethod) {
                $reflectionClassWithMethod = $this->dsm->getReflectionClass();
            }
            $setter   = $reflectionClassWithMethod->getMethod($setterName);
            $property = $this->dsm->getPropertyNameFromSetterName($setterName);
            $type     = $this->getPropertyTypeFromSetter($setter);
            $varType  = $this->getPropertyVarTypeAndAddImports($type, $setter, $reflectionClassWithMethod);
            $this->setProperty($property, $type, $varType);
            $this->setGetterFromPropertyAndType($getterName, $property, $type, $varType);
            $this->setSetterFromPropertyAndType($setterName, $property, $type, $varType);
            $this->addIssetMethodsForProperty($property, $type);
        }
    }

    private function getPropertyTypeFromSetter(ReflectionMethod $setter): string
    {
        /**
         * @var ReflectionParameter $param
         */
        $param = current($setter->getParameters());
        $type  = $param->getType();
        if ($type instanceof \ReflectionUnionType) {
            return (string)$type;
        }
        if (null !== $type) {
            $type = $type->getName();
            if (!in_array($type, ['string', 'bool', 'int', 'float', 'object', 'array'], true)) {
                $type = "\\$type";
            }
        }
        if (str_contains($type, 'Entity\Interfaces\\')) {
            $dtoFqn = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn(
                $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($type)
            );
            $type   = "$type|$dtoFqn";
        }

        $type = "null|$type";

        return (string)$type;
    }

    private function setProperty(string $property, string $type, string $varType): void
    {
        $defaultValue = $this->getDefaultValueCodeForProperty($property);
        $code         = '';
        if ('' !== $varType) {
            $code .= "\n" . '    /**';
            $code .= "\n" . '     * @var ' . $varType;
            $code .= "\n" . '     */';
        }
        $code .= "\n" . '    private ' . $type . ' $' . $property . ' = ' . $defaultValue . ';';

        $this->properties[] = $code;
    }


    private function getDefaultValueCodeForProperty(
        string $property
    ): string {
        $defaultValueConst = 'DEFAULT_' . $this->codeHelper->consty($property);
        $fullValueString   = $this->entityFqn . '::' . $defaultValueConst;
        if (defined($fullValueString)) {
            return $this->dsm->getShortName() . '::' . $defaultValueConst;
        }

        return 'null';
    }

    private function getPropertyVarTypeAndAddImports(
        string $type,
        ReflectionMethod $setter,
        ReflectionClass $reflectionClass
    ): string {
        if (!$this->typeHelper->isIterableType($type)) {
            return '';
        }
        $docComment = $setter->getDocComment();
        //look for iterable type doc comment
        if (preg_match('% ([^ ]+?)<([^>]+?)>%', $docComment, $matches) === 1) {
            $iterableType = $matches[1];
            $iteratedType = $matches[2];
            if ($this->typeHelper->isImportableType($iteratedType)) {
                $this->imports[] = $this->reflectionHelper->getUseStatementForShortName(
                    $iterableType,
                    $reflectionClass
                );
                $this->imports[] = $this->reflectionHelper->getUseStatementForShortName(
                    $iteratedType,
                    $reflectionClass
                );
            }

            return $iterableType . '<' . $iteratedType . '>';
        }

        throw new \RuntimeException('Failed finding iterable doc type comment in setter ' . $setter->getName());
    }

    private function nullable(string $type): string
    {
        if (str_contains($type, 'null')) {
            return $type;
        }

        return "null|$type";
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @param string $getterName
     * @param string $property
     * @param string $type
     */
    private function setGetterFromPropertyAndType(
        string $getterName,
        string $property,
        string $type,
        string $varType
    ): void {
        $code = '';
        if ('' !== $varType) {
            $code .= "\n" . '    /**';
            $code .= "\n" . '     * @return ' . $varType;
            $code .= "\n" . '     */';
        }
        if (\ts\stringContains($type, '\\Entity\\Interfaces\\')) {
            $type = $this->getEntityInterfaceFromCompoundType($type);
        }
        $code .= "\n    public function $getterName()" . (('' !== $type) ? ": {$this->nullable($type)}" : '');
        $code .= "\n    {";
        $code .= $this->getGetterBody($property, $type);
        $code .= "\n    }\n";

        $this->getters[] = $code;
    }

    private function getGetterBody(
        string $property,
        string $type
    ): string {
        if (str_contains($type, Collection::class)) {
            return "\n        return \$this->$property ?? \$this->$property = new ArrayCollection();";
        }
        if (\ts\stringContains($type, '\\Entity\\Interfaces\\')) {
            $getterCode = '';
            $getterCode .= "\n        if(null === \$this->$property){";
            $getterCode .= "\n            return \$this->$property;";
            $getterCode .= "\n        }";
            $getterCode .= "\n        if(\$this->$property instanceof $type){";
            $getterCode .= "\n            return \$this->$property;";
            $getterCode .= "\n        }";
            $getterCode .= "\n        throw new \RuntimeException(";
            $getterCode .= "\n            '\$this->$property is not an Entity, but is '. \$this->$property::class";
            $getterCode .= "\n        );";

            return $getterCode;
        }

        return "\n        return \$this->$property;";
    }

    private function setSetterFromPropertyAndType(
        string $setterName,
        string $property,
        string $type,
        string $varType
    ): void {
        $code = '';
        if ('' !== $varType) {
            $code .= "\n    /**";
            $code .= "\n     * @param $varType \$$property";
            $code .= "\n     */";
        }
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

    private function getEntityInterfaceFromCompoundType(string $type): string
    {
        preg_match('%([^|]+Entity\\\\Interfaces\\\\[^|]+)%', $type, $matches);

        return $matches[1] ?? throw new \RuntimeException(
                'Failed finding entity interface from type ' . $type . ' in ' . __METHOD__
            );
    }

    private function getDtoFromCompoundType(string $type): string
    {
        preg_match('%([^|]+Entity.+?Dto[^|]+)%', $type, $matches);

        return $matches[1] ?? throw new \RuntimeException(
                'Failed finding DTO from type ' . $type . ' in ' . __METHOD__
            );
    }


    private function setDtoGetterAndSetterForEntityProperty(
        string $setterName,
        string $property,
        string $type
    ): void {
        $entityInterfaceFqn = $this->getEntityInterfaceFromCompoundType($type);
        $dtoFqn             = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn(
            $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($entityInterfaceFqn)
        );
        $setterCode         = '';
        $setterCode         .= "\n    public function ${setterName}Dto($dtoFqn \$$property): self ";
        $setterCode         .= "\n    {";
        $setterCode         .= "\n        \$this->$property = \$$property;";
        $setterCode         .= "\n        return \$this;";
        $setterCode         .= "\n    }\n";
        $this->setters[]    = $setterCode;

        $getterName      = 'get' . substr($setterName, 3);
        $getterCode      = '';
        $getterCode      .= "\n    public function $getterName" . "Dto(): " . $this->nullable($dtoFqn);
        $getterCode      .= "\n    {";
        $getterCode      .= "\n        if(null === \$this->$property){";
        $getterCode      .= "\n            return \$this->$property;";
        $getterCode      .= "\n        }";
        $getterCode      .= "\n        if(\$this->$property instanceof $dtoFqn){";
        $getterCode      .= "\n            return \$this->$property;";
        $getterCode      .= "\n        }";
        $getterCode      .= "\n        throw new \RuntimeException(";
        $getterCode      .= "\n            '\$this->$property is not a DTO, but is '. \$this->$property::class";
        $getterCode      .= "\n        );";
        $getterCode      .= "\n    }\n";
        $this->getters[] = $getterCode;
    }

    private function addIssetMethodsForProperty(string $property, string $type): void
    {
        if (false === \ts\stringContains($type, '\\Entity\\Interfaces\\')) {
            return;
        }
        $methodName      = 'isset' . ucfirst($property) . 'AsDto';
        $getterCodeDto   = '';
        $getterCodeDto   .= "\n    public function $methodName(): bool";
        $getterCodeDto   .= "\n    {";
        $getterCodeDto   .= "\n        return isset(\$this->$property) && \$this->$property instanceof DataTransferObjectInterface;";
        $getterCodeDto   .= "\n    }\n";
        $this->getters[] = $getterCodeDto;

        $methodName       = 'isset' . ucfirst($property) . 'AsEntity';
        $getterCodeEntity = '';
        $getterCodeEntity .= "\n    public function $methodName(): bool";
        $getterCodeEntity .= "\n    {";
        $getterCodeEntity .= "\n       return isset(\$this->$property) && \$this->$property instanceof EntityInterface;";
        $getterCodeEntity .= "\n    }\n";
        $this->getters[]  = $getterCodeEntity;
    }

    private function updateFileContents(
        File\FindReplace $findReplace
    ): void {
        sort($this->properties, SORT_STRING);
        sort($this->getters, SORT_STRING);
        sort($this->setters, SORT_STRING);

        $body = implode("\n", $this->properties) .
                "\n\n" .
                implode("\n", $this->getters) .
                "\n" .
                implode("\n", $this->setters);

        $findReplace->findReplaceRegex('%{(.+)}%s', "{\n\$1\n$body\n}", 1);

        $imports = implode("\n", array_unique($this->imports));
        $findReplace->findReplaceRegex("%^(use .+?)\n\n/\*\*%sm", "$imports\n\n/**", 1);
    }
}

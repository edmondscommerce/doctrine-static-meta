<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\Src\Entity;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use ts\Reflection\ReflectionClass;

class CreateDataTransferObjectBodyProcess implements ProcessInterface
{
    /**
     * @var DoctrineStaticMeta
     */
    private $dsm;
    /**
     * @var ReflectionHelper
     */
    private $reflectionHelper;

    private $imports = [];

    private $properties = [];

    private $setters = [];

    private $getters = [];

    public function __construct(ReflectionHelper $reflectionHelper)
    {
        $this->reflectionHelper = $reflectionHelper;
    }

    public function setEntityFqn(string $entityFqn)
    {
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
        $this->writeCodeToFile($findReplace);
    }

    private function buildArraysOfCode()
    {
        $getters = $this->dsm->getGetters();
        foreach ($this->dsm->getSetters() as $getterName => $setterName) {
            $trait = $this->reflectionHelper->getTraitImplementingMethod(
                $this->dsm->getReflectionClass(),
                $setterName
            );
            $this->addImportsFromTrait($trait);
            $setter = $trait->getMethod($setterName);
            list($property, $type) = $this->getPropertyNameAndTypeFromSetter($setter);
            $this->setProperty($property, $type);
            $this->setSetterBodyFromTrait($trait, $setterName, $property);
            $this->setGetterBodyFromTrait($trait, $property, $getters);
        }

    }

    private function addImportsFromTrait(ReflectionClass $trait): void
    {
        foreach ($this->reflectionHelper->getUseStatements($trait) as $import) {
            $this->imports[$import] = $import;
        }
    }

    private function getPropertyNameAndTypeFromSetter(\ReflectionMethod $setter): array
    {
        /**
         * @var \ReflectionParameter $param
         */
        $param    = current($setter->getParameters());
        $property = $param->getName();
        $type     = $param->getType();
        if (null !== $type) {
            $type = $type->getName();
        }

        return [$property, (string)$type];
    }

    private function setProperty(string $property, string $type): void
    {
        $code               = '';
        $code               .= "\n" . '    /**';
        $code               .= "\n" . '     * @var ' . $type;
        $code               .= "\n" . '     */';
        $code               .= "\n" . '    private $' . $property . ';';
        $this->properties[] = $code;
    }

    private function setSetterBodyFromTrait(ReflectionClass $trait, string $setterName, string $property): void
    {
        $methodBody      = $this->reflectionHelper->getMethodBody($setterName, $trait);
        $methodBody      = preg_replace(
            '%{.+}%s',
            "{\n        \$this->$property=\$$property;\n        return \$this;\n    }",
            $methodBody
        );
        $methodBody      = str_replace('private function', 'public function', $methodBody);
        $this->setters[] = $methodBody;

    }

    private function setGetterBodyFromTrait(
        ReflectionClass $trait,
        string $property,
        string $getterName
    ): void {
        $methodBody = $this->reflectionHelper->getMethodBody($getterName, $trait);
        $methodBody = preg_replace(
            '%\{.+\}%s',
            "{\n        return \$this->$property;\n    }",
            $methodBody
        );

        $this->getters[] = $methodBody;
    }

    private function writeCodeToFile(File\FindReplace $findReplace)
    {
        $body = '{'
                . implode("\n", $this->properties) .
                "\n\n" .
                implode("\n", $this->getters) .
                "\n" .
                implode("\n", $this->setters) .
                "\n}";

        $findReplace->findReplaceRegex('%{.+?}%s', $body);
    }


}
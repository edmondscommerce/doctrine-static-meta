<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Traits;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\FindReplaceProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\AbstractFieldCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use RuntimeException;

class FieldTraitCreator extends AbstractFieldCreator
{

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                 '/src/Entity/Fields/Traits/' .
                                 self::FIND_NAME . 'FieldTrait.php';


    public const SUFFIX           = 'FieldTrait';
    public const INTERFACE_SUFFIX = 'FieldInterface';
    public const FIND_METHOD      = 'MappingHelper::setSimpleStringFields';


    protected function configurePipeline(): void
    {
        parent::configurePipeline();
        $this->registerReplaceMappingHelperMethod();
        $this->registerReplaceType();
        $this->registerSetValidationForString();
        $this->registerReplaceInterfaceName();
        $this->registerUpdateWithUnique();
        $this->registerReplacePropertyName();
    }

    private function registerReplaceMappingHelperMethod(): void
    {
        $methodName = 'setSimple' . ucfirst($this->mappingHelperType) . 'Fields';
        if (false === method_exists(MappingHelper::class, $methodName)) {
            throw new RuntimeException('Invalid method name ' . $methodName . ' not found in MappingHelper');
        }
        $replaceMethod = 'MappingHelper::' . $methodName;
        $process       = new FindReplaceProcess(self::FIND_METHOD, $replaceMethod);
        $this->pipeline->register($process);
    }

    private function registerSetValidationForString(): void
    {
        if ($this->mappingHelperType !== MappingHelper::TYPE_STRING) {
            return;
        }
        $this->pipeline->register(new FindReplaceProcess(
            <<<'TEXT'
//        $metadata->addPropertyConstraint(
//            TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME,
//            new NotBlank()
//        );
TEXT
                                      ,
            <<<'TEXT'
        $metadata->addPropertyConstraint(
            TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME,
            new Length(['min' => 0, 'max' => Database::MAX_VARCHAR_LENGTH])
        );
TEXT
        ));
        $this->pipeline->register(
            new FindReplaceProcess(
                <<<'TEXT'

trait
TEXT
                ,
                <<<'TEXT'
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use Symfony\Component\Validator\Constraints\Length;                

trait
TEXT
            )
        );
    }


    private function registerReplaceInterfaceName(): void
    {
        $interfaceName =
            str_replace(self::SUFFIX, self::INTERFACE_SUFFIX, $this->baseName);
        $replaceName   = new ReplaceNameProcess();
        $replaceName->setArgs(self::FIND_NAME . self::INTERFACE_SUFFIX, $interfaceName);
        $this->pipeline->register($replaceName);
    }

    private function registerUpdateWithUnique(): void
    {
        if (in_array($this->mappingHelperType, MappingHelper::UNIQUEABLE_TYPES, true)) {
            $isUniqueString = $this->isUnique ? 'true' : 'false';
            $process        = new class ($isUniqueString) implements ProcessInterface
            {
                /**
                 * @var string
                 */
                private $isUniqueString;

                public function __construct(string $isUniqueString)
                {
                    $this->isUniqueString = $isUniqueString;
                }

                public function run(File\FindReplace $findReplace): void
                {
                    $findReplace->findReplaceRegex(
                        "%MappingHelper(.+?)\n        \);%s",
                        "MappingHelper$1,\n            " . $this->isUniqueString . "\n        );"
                    );
                }
            };
            $this->pipeline->register($process);
        }
    }
}

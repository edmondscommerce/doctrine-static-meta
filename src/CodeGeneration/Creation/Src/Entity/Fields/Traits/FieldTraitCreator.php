<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Traits;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\AbstractCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ProcessInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Process\ReplaceNameProcess;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\AbstractFieldCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use InvalidArgumentException;
use function strlen;

class FieldTraitCreator extends AbstractFieldCreator
{

    public const TEMPLATE_PATH = self::ROOT_TEMPLATE_PATH .
                                 '/src/Entity/Fields/Traits/' .
                                 self::FIND_NAME . 'FieldTrait.php';


    public const SUFFIX           = 'FieldTrait';
    public const INTERFACE_SUFFIX = 'FieldInterface';
    public const FIND_METHOD      = 'MappingHelper::setSimpleStringFields';



    public function setNewObjectFqn(string $newObjectFqn): AbstractCreator
    {
        $this->validateFqnEndsWithSuffix($newObjectFqn);

        return parent::setNewObjectFqn($newObjectFqn);
    }

    private function validateFqnEndsWithSuffix(string $newObjectFqn): void
    {
        if (substr($newObjectFqn, 0 - strlen(self::SUFFIX)) === self::SUFFIX) {
            return;
        }
        throw new InvalidArgumentException('$newObjectFqn must end in ' . self::SUFFIX);
    }



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
            throw new \RuntimeException('Invalid method name ' . $methodName . ' not found in MappingHelper');
        }
        $replaceMethod = 'MappingHelper::' . $methodName;
        $process       = new class(self::FIND_METHOD, $replaceMethod) implements ProcessInterface
        {
            /**
             * @var string
             */
            private $find;
            /**
             * @var string
             */
            private $replace;

            /**
             * @param string $find
             * @param string $replace
             */
            public function __construct(string $find, string $replace)
            {
                $this->find    = $find;
                $this->replace = $replace;
            }

            public function run(File\FindReplace $findReplace): void
            {
                $findReplace->findReplace($this->find, $this->replace);
            }
        };
        $this->pipeline->register($process);
    }

    private function registerSetValidationForString(): void
    {
        if ($this->mappingHelperType !== MappingHelper::TYPE_STRING) {
            return;
        }
        $process = new class implements ProcessInterface
        {
            public function run(File\FindReplace $findReplace): void
            {
                $this->updateValidator($findReplace);
                $this->addUseStatements($findReplace);
            }

            private function updateValidator(File\FindReplace $findReplace): void
            {
                $find    = <<<'TEXT'
//        $metadata->addPropertyConstraint(
//            TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME,
//            new NotBlank()
//        );
TEXT;
                $replace = <<<'TEXT'
        $metadata->addPropertyConstraint(
            TemplateFieldNameFieldInterface::PROP_TEMPLATE_FIELD_NAME,
            new Length(['min' => 0, 'max' => Database::MAX_VARCHAR_LENGTH])
        );
TEXT;
                $findReplace->findReplace($find, $replace);
            }

            private function addUseStatements(File\FindReplace $findReplace): void
            {
                $find    = <<<'TEXT'

trait
TEXT;
                $replace = <<<'TEXT'
use \EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use \Symfony\Component\Validator\Constraints\Length;                

trait
TEXT;
                $findReplace->findReplace($find, $replace);
            }
        };
        $this->pipeline->register($process);
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
        if (in_array($this->phpType, MappingHelper::UNIQUEABLE_TYPES, true)) {
            $isUniqueString = $this->isUnique ? 'true' : 'false';
            $process        = new class($isUniqueString) implements ProcessInterface
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

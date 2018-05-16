<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFieldCommand extends AbstractCommand
{

    public const OPT_FQN         = 'field-fully-qualified-name';
    public const OPT_FQN_SHORT   = 'f';
    public const DEFINITION_NAME = 'The fully qualified name of the property you want to generate';

    public const OPT_TYPE        = 'field-property-doctrine-type';
    public const OPT_TYPE_SHORT  = 'y';
    public const DEFINITION_TYPE = 'The data type of the property you want to generate';

    public const OPT_DEFAULT_VALUE        = 'default';
    public const OPT_DEFAULT_VALUE_SHORT  = 'd';
    public const DEFINITION_DEFAULT_VALUE = 'The default value, defaults to null '
                                            .'(which also marks the field as nullable)';

    public const OPT_IS_UNIQUE        = 'is-unique';
    public const OPT_IS_UNIQUE_SHORT  = 'u';
    public const DEFINITION_IS_UNIQUE = 'This field is unique, duplicates are not allowed';

    /**
     * @var FieldGenerator
     */
    protected $fieldGenerator;

    /**
     * GenerateEntityCommand constructor.
     *
     * @param FieldGenerator  $fieldGenerator
     * @param NamespaceHelper $namespaceHelper
     * @param null|string     $name
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function __construct(
        FieldGenerator $fieldGenerator,
        NamespaceHelper $namespaceHelper,
        ?string $name = null
    ) {
        parent::__construct($namespaceHelper, $name);
        $this->fieldGenerator = $fieldGenerator;
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function configure(): void
    {
        try {
            $this
                ->setName(AbstractCommand::COMMAND_PREFIX.'generate:field')
                ->setDefinition(
                    [
                        new InputOption(
                            self::OPT_FQN,
                            self::OPT_FQN_SHORT,
                            InputOption::VALUE_REQUIRED,
                            self::DEFINITION_NAME
                        ),
                        new InputOption(
                            self::OPT_TYPE,
                            self::OPT_TYPE_SHORT,
                            InputOption::VALUE_REQUIRED,
                            self::DEFINITION_TYPE
                        ),
                        new InputOption(
                            self::OPT_DEFAULT_VALUE,
                            self::OPT_DEFAULT_VALUE_SHORT,
                            InputOption::VALUE_REQUIRED,
                            self::DEFINITION_DEFAULT_VALUE,
                            null
                        ),
                        new InputOption(
                            self::OPT_IS_UNIQUE,
                            self::OPT_IS_UNIQUE_SHORT,
                            InputOption::VALUE_NONE,
                            self::DEFINITION_IS_UNIQUE
                        ),
                        $this->getProjectRootPathOption(),
                        $this->getProjectRootNamespaceOption(),
                        $this->getSrcSubfolderOption(),
                        $this->getTestSubFolderOption(),
                    ]
                )->setDescription(
                    'Generate a field'
                );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws DoctrineStaticMetaException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->checkOptions($input);

            $output->writeln(
                '<comment>Starting generation for '.$input->getOption(self::OPT_FQN).'</comment>'
            );

            $this->fieldGenerator
                ->setPathToProjectRoot($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH))
                ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE))
                ->setTestSubFolderName($input->getOption(AbstractCommand::OPT_TEST_SUBFOLDER));

            $this->fieldGenerator->generateField(
                $input->getOption(self::OPT_FQN),
                $input->getOption(self::OPT_TYPE),
                null,
                $input->getOption(self::OPT_DEFAULT_VALUE),
                $input->getOption(self::OPT_IS_UNIQUE)
            );

            $output->writeln('<info>completed</info>');
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}

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

    public const OPT_NAME        = 'field-property-name';
    public const OPT_NAME_SHORT  = 'f';
    public const DEFINITION_NAME = 'The name of the property you want to generate';

    public const OPT_TYPE        = 'field-property-data-type';
    public const OPT_TYPE_SHORT  = 'd';
    public const DEFINITION_TYPE = 'The data type of the property you want to generate';

    /**
     * @var FieldGenerator
     */
    protected $fieldGenerator;

    /**
     * GenerateEntityCommand constructor.
     *
     * @param FieldGenerator $fieldGenerator
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
                            self::OPT_NAME,
                            self::OPT_NAME_SHORT,
                            InputOption::VALUE_REQUIRED,
                            self::DEFINITION_NAME
                        ),
                        new InputOption(
                            self::OPT_TYPE,
                            self::OPT_TYPE_SHORT,
                            InputOption::VALUE_REQUIRED,
                            self::DEFINITION_TYPE
                        ),
                        $this->getProjectRootPathOption(),
                        $this->getProjectRootNamespaceOption(),
                        $this->getSrcSubfolderOption(),
                        $this->getTestSubFolderOption()
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
                '<comment>Starting generation for '.$input->getOption(self::OPT_NAME).'</comment>'
            );

            $this->fieldGenerator
                ->setPathToProjectRoot($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH))
                ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE))
                ->setTestSubFolderName($input->getOption(AbstractCommand::OPT_TEST_SUBFOLDER));

            $this->fieldGenerator->generateField(
                $input->getOption(self::OPT_NAME),
                $input->getOption(self::OPT_TYPE)
            );

            $output->writeln('<info>completed</info>');
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityCommand extends AbstractCommand
{

    const OPT_FQN        = 'entity-fully-qualified-name';
    const OPT_FQN_SHORT  = 'f';
    const DEFINITION_FQN = 'The fully qualified name of the entity you want to create';

    protected function configure()
    {
        $this
            ->setName(AbstractCommand::COMMAND_PREFIX . 'generate:entity')
            ->setDefinition(
                [
                    $this->getProjectRootPathOption(),
                    $this->getProjectRootNamespaceOption(),
                    $this->getProjectEntitiesRootNamespaceOption(),
                    new InputOption(
                        self::OPT_FQN,
                        self::OPT_FQN_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEFINITION_FQN
                    ),
                    new InputOption(
                        self::OPT_SRC_SUBFOLDER,
                        self::OPT_SRC_SUBFOLDER_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEFINITION_SRC_SUBFOLDER,
                        self::DEFAULT_SRC_SUBFOLDER
                    ),
                    new InputOption(
                        self::OPT_TEST_SUBFOLDER,
                        self::OPT_TEST_SUBFOLDER_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEFINITION_TEST_SUBFOLDER,
                        self::DEFAULT_TEST_SUBFOLDER
                    ),
                ]
            )->setDescription(
                'Generate an Entity'
            );

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkAllRequiredOptionsAreNotEmpty($input);
        $output->writeln('<comment>Starting generation for ' . $input->getOption(self::OPT_FQN) . '</comment>');
        (new EntityGenerator(
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE),
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH),
            $input->getOption(self::OPT_ENTITIES_ROOT_NAMESPACE)
        ))->generateEntity($input->getOption(self::OPT_FQN));
        $output->writeln('<info>completed</info>');
    }


}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityCommand extends AbstractCommand
{

    const ARG_FQN = 'entity-fully-qualified-name';
    const ARG_FQN_SHORT = 'fqn';
    const DEFINITION_FQN = 'The fully qualified name of the entity you want to create';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_PREFIX.'generate:entity')
            ->setDefinition(
                array(
                    new InputOption(
                        self::ARG_PROJECT_ROOT_PATH,
                        self::ARG_PROJECT_ROOT_PATH_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEFINITION_PROJECT_ROOT_PATH
                    ),
                    new InputOption(
                        self::ARG_PROJECT_ROOT_NAMESPACE,
                        self::ARG_PROJECT_ROOT_NAMESPACE_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEFINITION_PROJECT_ROOT_NAMESPACE
                    ),
                    new InputOption(
                        self::ARG_FQN,
                        self::ARG_FQN_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEFINITION_FQN
                    ),
                )
            );

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        (new EntityGenerator(
            $input->getOption(self::ARG_PROJECT_ROOT_NAMESPACE),
            $input->getOption(self::ARG_PROJECT_ROOT_PATH)
        ))->generateEntity($input->getOption(self::ARG_FQN));
    }


}

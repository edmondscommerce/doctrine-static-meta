<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\EntityFormatter;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FinaliseBuildCommand extends AbstractCommand
{
    /**
     * @var CreateDtosForAllEntitiesAction
     */
    private $action;
    /**
     * @var EntityFormatter
     */
    private $entityFormatter;

    public function __construct(
        CreateDtosForAllEntitiesAction $action,
        EntityFormatter $entityFormatter,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->action          = $action;
        $this->entityFormatter = $entityFormatter;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->checkOptions($input);
            $output->writeln(
                '<comment>Starting generation of data transfer objects for all entities</comment>'
            );
            $this->action->setProjectRootNamespace($input->getOption(self::OPT_PROJECT_ROOT_NAMESPACE))
                         ->setProjectRootDirectory($input->getOption(self::OPT_PROJECT_ROOT_PATH))
                         ->run();
            $output->writeln(
                '<comment>Formatting Entities</comment>'
            );
            $this->entityFormatter->setPathToProjectRoot($input->getOption(self::OPT_PROJECT_ROOT_PATH))
                                  ->run();
            $output->writeln('<info>completed</info>');
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function configure(): void
    {
        try {
            $this
                ->setName(AbstractCommand::COMMAND_PREFIX . 'finalise:build')
                ->setDefinition(
                    [
                        $this->getProjectRootPathOption(),
                        $this->getProjectRootNamespaceOption(),
                    ]
                )->setDescription(
                    'Generate or update all Data Transfer Objects for all Entities and perform other post processes'
                );
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}

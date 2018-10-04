<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDataTransferObjectsFromEntitiesCommand extends AbstractCommand
{
    /**
     * @var CreateDtosForAllEntitiesAction
     */
    private $action;

    public function __construct(CreateDtosForAllEntitiesAction $action, ?string $name = null)
    {
        parent::__construct($name);
        $this->action = $action;
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
            $output->writeln('<info>completed</info>');
        } catch (\Exception $e) {
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
                ->setName(AbstractCommand::COMMAND_PREFIX . 'generate:dtos-for-entities')
                ->setDefinition(
                    [
                        $this->getProjectRootPathOption(),
                        $this->getProjectRootNamespaceOption(),
                    ]
                )->setDescription(
                    'Generate or update all Data Transfer Objects for all Entities'
                );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}

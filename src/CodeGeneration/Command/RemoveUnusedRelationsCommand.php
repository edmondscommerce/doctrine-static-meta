<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveUnusedRelationsCommand extends AbstractCommand
{
    /**
     * @var UnusedRelationsRemover
     */
    protected $remover;

    public function __construct(UnusedRelationsRemover $remover, ?string $name = null)
    {
        parent::__construct($name);
        $this->remover = $remover;
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    public function configure(): void
    {
        try {
            $this->setName(AbstractCommand::COMMAND_PREFIX . 'finalise:remove-unused-relations')
                 ->setAliases([
                                  AbstractCommand::COMMAND_PREFIX . 'remove:unusedRelations',
                              ])
                 ->setDefinition(
                     [
                         $this->getProjectRootPathOption(),
                         $this->getProjectRootNamespaceOption(),
                     ]
                 )->setDescription(
                     'Find and remove unused relations traits and interfaces'
                 );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws DoctrineStaticMetaException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln(
                '<comment>Finding and Removing Unused Generated Code</comment>'
            );
            $this->checkOptions($input);
            $removedFiles = $this->remover
                ->setPathToProjectRoot($input->getOption(self::OPT_PROJECT_ROOT_PATH))
                ->setProjectRootNamespace($input->getOption(self::OPT_PROJECT_ROOT_NAMESPACE))
                ->run();
            $output->writeln('<comment>Removed ' . count($removedFiles) . ' Files:</comment>');
            $output->writeln(print_r($removedFiles, true));
            $output->writeln('<info>completed</info>');
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}

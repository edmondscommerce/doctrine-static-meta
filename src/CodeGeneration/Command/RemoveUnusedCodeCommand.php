<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedCodeRemover;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveUnusedCodeCommand extends AbstractCommand
{
    /**
     * @var UnusedCodeRemover
     */
    protected $remover;

    public function __construct(UnusedCodeRemover $remover, NamespaceHelper $namespaceHelper, ?string $name = null)
    {
        parent::__construct($namespaceHelper, $name);
        $this->remover = $remover;
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    public function configure(): void
    {
        try {
            $this->setName(AbstractCommand::COMMAND_PREFIX.'remove:unusedCode')
                 ->setDefinition(
                     [
                         $this->getProjectRootPathOption(),
                         $this->getProjectRootNamespaceOption(),
                     ]
                 )->setDescription(
                    'Find and remove unused code, such as relations traits'
                );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.': '.$e->getMessage(),
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
            $removedFiles = $this->remover->run();
            $output->writeln('<comment>Removed '.count($removedFiles).' Files:</comment>');
            $output->writeln(print_r($removedFiles, true));
            $output->writeln('<info>completed</info>');
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.': '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OverridesUpdateCommand extends AbstractCommand
{
    public const OPT_OVERRIDE_ACTION       = 'action';
    public const OPT_OVERRIDE_ACTION_SHORT = 'a';

    public const ACTION_TO_PROJECT   = 'toProject';
    public const ACTION_FROM_PROJECT = 'fromProject';

    /**
     * @var FileOverrider
     */
    protected $fileOverrider;

    public function __construct(FileOverrider $fileOverrider, NamespaceHelper $namespaceHelper, ?string $name = null)
    {
        parent::__construct($namespaceHelper, $name);
        $this->fileOverrider = $fileOverrider;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fileOverrider->setPathToProjectRoot($input->getOption(self::OPT_PROJECT_ROOT_PATH));
        switch ($input->getOption(self::OPT_OVERRIDE_ACTION)) {
            case self::ACTION_TO_PROJECT:
                $this->renderTableOfUpdatedFiles($this->fileOverrider->applyOverrides(), $output);
                $output->writeln('<info>Overrides have been applied to project</info>');
                break;
            case self::ACTION_FROM_PROJECT:
                $this->renderTableOfUpdatedFiles($this->fileOverrider->updateOverrideFiles(), $output);
                $output->writeln('<info>Overrides have been updated froms project</info>');
                break;
            default:
                throw new \InvalidArgumentException(
                    ' Invalid action ' . $input->getOption(self::OPT_OVERRIDE_ACTION)
                );
        }
    }

    private function renderTableOfUpdatedFiles(array $files, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setRows($files);
        $table->render();
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function configure(): void
    {
        try {
            $this
                ->setName(AbstractCommand::COMMAND_PREFIX . 'overrides:update')
                ->setDefinition(
                    [
                        new InputOption(
                            self::OPT_OVERRIDE_ACTION,
                            self::OPT_OVERRIDE_ACTION_SHORT,
                            InputOption::VALUE_REQUIRED,
                            'One of [ fromProject,  toProject ]'
                        ),
                        $this->getProjectRootPathOption(),
                    ]
                )->setDescription(
                    'Update project overrides'
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

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OverrideCreateCommand extends AbstractCommand
{
    public const OPT_OVERRIDE_FILE       = 'file';
    public const OPT_OVERRIDE_FILE_SHORT = 'f';

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
        $pathCreated = $this->fileOverrider->createNewOverride($input->getOption(self::OPT_OVERRIDE_FILE));
        $output->writeln('<info>Override created at: ' . $pathCreated . '</info>');
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function configure(): void
    {
        try {
            $this
                ->setName(AbstractCommand::COMMAND_PREFIX . 'overrides:create')
                ->setDefinition(
                    [
                        new InputOption(
                            self::OPT_OVERRIDE_FILE,
                            self::OPT_OVERRIDE_FILE_SHORT,
                            InputOption::VALUE_REQUIRED,
                            'the absolute path of the project file you want to override'
                        ),
                        $this->getProjectRootPathOption(),
                    ]
                )->setDescription(
                    'Create new overrides for project files'
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

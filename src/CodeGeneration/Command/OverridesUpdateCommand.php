<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    public function __construct(FileOverrider $fileOverrider, ?string $name = null)
    {
        parent::__construct($name);
        $this->fileOverrider = $fileOverrider;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $this->checkOptions($input);
        $output->writeln(
            '<comment>Updating overrides ' . $input->getOption(self::OPT_OVERRIDE_ACTION) . '</comment>'
        );
        $this->checkOptions($input);
        $this->fileOverrider->setPathToProjectRoot($input->getOption(self::OPT_PROJECT_ROOT_PATH));
        switch ($input->getOption(self::OPT_OVERRIDE_ACTION)) {
            case self::ACTION_TO_PROJECT:
                $invalidOverrides = $this->fileOverrider->getInvalidOverrides();
                if ([] !== $invalidOverrides) {
                    $symfonyStyle->error('Some Overrides are Invalid');
                    $fixed = $this->renderInvalidOverrides($invalidOverrides, $output, $symfonyStyle);
                    if (false === $fixed) {
                        throw new \RuntimeException('Errors in applying overrides');
                    }
                }
                $this->renderTableOfUpdatedFiles($this->fileOverrider->applyOverrides(), $output);
                $output->writeln('<info>Overrides have been applied to project</info>');

                return;
            case self::ACTION_FROM_PROJECT:
                $this->renderTableOfUpdatedFiles($this->fileOverrider->updateOverrideFiles(), $output);
                $output->writeln('<info>Overrides have been updated from the project</info>');

                return;
            default:
                throw new \InvalidArgumentException(
                    ' Invalid action ' . $input->getOption(self::OPT_OVERRIDE_ACTION)
                );
        }
    }

    private function renderInvalidOverrides(
        array $invalidOverrides,
        OutputInterface $output,
        SymfonyStyle $symfonyStyle
    ): bool {
        $return = false;
        foreach ($invalidOverrides as $pathToFileInOverrides => $details) {
            $return = $this->processInvalidOverride($pathToFileInOverrides, $details, $output, $symfonyStyle);
        }

        return $return;
    }

    private function processInvalidOverride(
        string $relativePathToFileInOverrides,
        array $details,
        OutputInterface $output,
        SymfonyStyle $symfonyStyle
    ): bool {
        $symfonyStyle->title('Working on ' . basename($relativePathToFileInOverrides));
        $output->writeln('<comment>' . $relativePathToFileInOverrides . '</comment>');
        $symfonyStyle->section('Details');
        $table = new Table($output);
        $table->setHeaders(['Key', 'Value']);
        $table->addRows(
            [
                ['Project File', $details['projectPath']],
                ['Override File', $details['overridePath']],
                ['New MD5', $details['new md5']],
                ['Diff Size', substr_count($details['diff'], "\n")],
            ]
        );
        $table->render();
        $output->writeln('<info>Diff:</info>');
        $output->write($details['diff']);
        $output->writeln("\n\n");
        $output->writeln('<info>Fixing this</info>');
        $output->writeln(<<<TEXT
        
The suggested fix in this situation is:

 * Rename the current override
 * Make a new override from the newly generated file 
 * Reapply your custom code to the new override
 * Finally delete the old override.
 
TEXT
        );
        if (!$symfonyStyle->ask('Would you like to move the current override and make a new one and then diff this?',
                                true)) {
            $output->writeln('<commment>Skipping ' . $relativePathToFileInOverrides . '</commment>');

            return false;
        }

        $symfonyStyle->section('Recreating Override');
        list($old,) = $this->fileOverrider->recreateOverride($relativePathToFileInOverrides);

        $table = new Table($output);
        $table->addRow(['project file', $details['projectPath']]);
        $table->render();

        $table = new Table($output);
        $table->addRow(['old override', $old]);
        $table->render();

        $output->writeln(<<<TEXT
        
Now we have created a new override from your freshly generated file, 
you need to manually copy across all the changes from the old override into your project file.

* Open the project file
 
* In PHPStorm, find the old file, right click it and select "compare with editor"
 
TEXT
        );
        $symfonyStyle->caution('You must do this bit really carefully and exactly as instructed!!');

        while (
            false === $symfonyStyle->confirm(
                'Confirm you have now copied all required changes from the old override to the new one?',
                false
            )
        ) {
            $symfonyStyle->warning('You must now copy all required changes from the old override to the new one');
        }
        $symfonyStyle->section('Now updating override');
        $this->fileOverrider->updateOverrideFiles();
        $symfonyStyle->success("\n\nCompleted override update for $relativePathToFileInOverrides\n\n");

        return true;
    }

    private function renderTableOfUpdatedFiles(array $files, OutputInterface $output): void
    {
        list($updated, $same) = $files;
        if ([] !== $updated) {
            $output->writeln('Files Updated:');
            $table = new Table($output);
            foreach ($updated as $file) {
                $table->addRow([$file]);
            }
            $table->render();
        }
        if ([] !== $same) {
            $output->writeln('Files Same:');
            $table = new Table($output);
            foreach ($same as $file) {
                $table->addRow([$file]);
            }
            $table->render();
        }
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

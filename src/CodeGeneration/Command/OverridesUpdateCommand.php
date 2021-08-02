<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
use InvalidArgumentException;
use RuntimeException;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
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
                $this->actionOverridesToProject($symfonyStyle, $output);

                return 0;
            case self::ACTION_FROM_PROJECT:
                $this->actionOverridesFromProject($symfonyStyle, $output);

                return 0;
            default:
                throw new InvalidArgumentException(
                    ' Invalid action ' . $input->getOption(self::OPT_OVERRIDE_ACTION)
                );
        }
    }

    private function actionOverridesToProject(SymfonyStyle $symfonyStyle, OutputInterface $output): void
    {
        $invalidOverrides = $this->fileOverrider->getInvalidOverrides();
        if ([] !== $invalidOverrides) {
            $symfonyStyle->error('Some Overrides are Invalid');
            $symfonyStyle->note(<<<TEXT
                    
If you want to reset everything, you should do the following:

    [ctrl] + [c]
    git add -A :/
    git reset --hard HEAD

TEXT
            );
            $fixed = $this->renderInvalidOverrides($invalidOverrides, $output, $symfonyStyle);
            if (false === $fixed) {
                throw new RuntimeException('Errors in applying overrides');
            }
        }
        $this->renderTableOfUpdatedFiles($this->fileOverrider->applyOverrides(), $output);
        $output->writeln('<info>Overrides have been applied to project</info>');
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
        $symfonyStyle->newLine();
        $this->renderKeyValue(
            [
                'Project File'  => $details['projectPath'],
                'Override File' => $details['overridePath'],
                'New MD5'       => $details['new md5'],
                'Diff Size'     => substr_count($details['diff'], "\n"),
            ],
            $symfonyStyle
        );
        $output->writeln(<<<TEXT
        
<info>The suggested fix in this situation is:</info>

 * Rename the current override
 * Make a new override from the newly generated file 
 * Reapply your custom code to the project file
 * Finally delete the old override.
 
TEXT
        );
        if (
            !$symfonyStyle->ask(
                'Would you like to move the current override and make a new one and then diff this?',
                true
            )
        ) {
            $output->writeln('<commment>Skipping ' . $relativePathToFileInOverrides . '</commment>');

            return false;
        }

        $symfonyStyle->section('Recreating Override');
        [$old, $new] = $this->fileOverrider->recreateOverride($relativePathToFileInOverrides);
        $this->renderKeyValue(
            [
                'Old Override' => $old,
                'New Override' => $new,
            ],
            $symfonyStyle
        );
        $projectRoot = $this->fileOverrider->getPathToProjectRoot();
        $output->writeln(<<<TEXT
        
Now we have created a new override from your freshly generated file, 
you need to manually copy across all the required changes from the old override into your project file.

Run this command <comment>in another terminal</comment>:

    cd $projectRoot
    
    meld .$new .$old && rm -f .$old
 
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
        $symfonyStyle->success("\n\nCompleted override update for $relativePathToFileInOverrides\n\n");

        return true;
    }

    private function renderKeyValue(array $keysToValues, SymfonyStyle $symfonyStyle): void
    {
        $symfonyStyle->newLine();
        $longestKey = max(array_map('strlen', array_keys($keysToValues)));
        foreach ($keysToValues as $key => $value) {
            $key = str_pad($key, $longestKey, ' ');
            $symfonyStyle->writeln("<comment>$key:</comment> $value");
        }
        $symfonyStyle->newLine();
    }

    private function renderTableOfUpdatedFiles(array $files, OutputInterface $output): void
    {
        [$updated, $same] = $files;
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

    private function actionOverridesFromProject(SymfonyStyle $symfonyStyle, OutputInterface $output): void
    {
        [$filesDifferent,] = $this->fileOverrider->compareOverridesWithProject();
        if ([] === $filesDifferent) {
            $symfonyStyle->success('All override files are up to date, nothing else required');

            return;
        }
        $symfonyStyle->note(<<<TEXT

Some override files are not up to date with project file changes.
    
What we need to do now is to update the override files with the changes you have made in your project files.
               
TEXT
        );
        $action = $symfonyStyle->choice(
            'How would you like to resolve this?',
            [
                'process'            => 'Process each file one at a time and decide to copy or not',
                'copyAllFromProject' => 'Update all override files with the content of the project files',
                'skipAll'            => 'Do not update any override files, lose all changes on project files (danger!)',
            ],
            'Process each file one at a time and decide to copy or not'
        );
        switch ($action) {
            case 'copyAllFromProject':
                $toUpdate = $filesDifferent;
                break;
            case 'skipAll':
                $toUpdate = [];
                break;
            case 'process':
                $toUpdate = $this->processFilesChanges($filesDifferent, $symfonyStyle, $output);
                break;
            default:
                throw new RuntimeException('Invalid action ' . $action);
        }

        if ([] === $toUpdate) {
            $symfonyStyle->success('No updates to apply');

            return;
        }
        $this->renderTableOfUpdatedFiles($this->fileOverrider->updateOverrideFiles($toUpdate), $output);
        $output->writeln('<info>Overrides have been updated from the project</info>');
    }

    private function processFilesChanges(
        array $filesDifferent,
        SymfonyStyle $symfonyStyle,
        OutputInterface $output
    ): array {
        $toUpdate = [];
        foreach ($filesDifferent as $relativePathToFileInProject => $details) {
            $symfonyStyle->section('Processing ' . $relativePathToFileInProject);
//            $table = new Table($output);
//            $table->setHeaders(['Key', 'Value']);
//            $table->addRows(
//                [
//                    ['Project File', $relativePathToFileInProject],
//                    ['Override File', $details['overridePath']],
//                    ['Diff Size', substr_count($details['diff'], "\n")],
//                ]
//            );
//            $table->render();
            $this->renderKeyValue(
                [
                    'Project File'  => $relativePathToFileInProject,
                    'Override File' => $details['overridePath'],
                    'Diff Size'     => substr_count($details['diff'], "\n"),
                ],
                $symfonyStyle
            );
            $output->writeln('<info>Diff:</info>');
            $output->write($details['diff']);
            $output->writeln("\n\n");
            if (
                true === $symfonyStyle->ask(
                    'Would you like to copy the project file contents into the override file?',
                    true
                )
            ) {
                $symfonyStyle->success(
                    'adding ' . $relativePathToFileInProject .
                    ' to list of files that will be copied into the overrides'
                );
                $toUpdate[$relativePathToFileInProject] = true;
                continue;
            }
            $symfonyStyle->note(
                'skipping ' . $relativePathToFileInProject
                . ' from list of files that will be copied into the overrides'
            );
        }

        return $toUpdate;
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
        } catch (Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}

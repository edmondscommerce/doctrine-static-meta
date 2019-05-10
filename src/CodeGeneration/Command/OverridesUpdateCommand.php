<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
                    $output->writeln("<error>\n\n   Some Overrides are Invalid\n</error>");
                    $this->renderInvalidOverrides($invalidOverrides, $input, $output);
                    throw new \RuntimeException('Errors in applying overrides');

                    return;
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
        InputInterface $input,
        OutputInterface $output
    ): void {
        foreach ($invalidOverrides as $pathToFileInOverrides => $details) {
            $this->processInvalidOverride($pathToFileInOverrides, $details, $input, $output);
        }
    }

    private function processInvalidOverride(
        string $relativePathToFileInOverrides,
        array $details,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeln('<comment>' . $relativePathToFileInOverrides . '</comment>');
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
        $questionHelper = $this->getHelper('question');
        $question       = new ConfirmationQuestion(
            'Would you like to move the current override and make a new one and then diff this? (y/n) ',
            true
        );
        if (!$questionHelper->ask($input, $output, $question)) {
            $this->writeln('<commment>Skipping ' . $relativePathToFileInOverrides . '</commment>');

            return;
        }

        $output->writeln('<comment>Recreating Override</comment>');
        list($old, $new) = $this->fileOverrider->recreateOverride($relativePathToFileInOverrides);
        $table = new Table($output);
        $table->addRow(['old', $old]);
        $table->render();
        $table = new Table($output);
        $table->addRow(['new', $new]);
        $table->render();
        $output->writeln(<<<TEXT
        
Now we have created a new override from your freshly generated file, you need to manually copy across all the changes from the old override into your project file.

* Open the project file: {$details['projectPath']}
 
* In PHPStorm, find the old file, right click it and select "compare with editor"
 
TEXT
        );
        $question = new ConfirmationQuestion(
            'Confirm you have now copied all required changes from the old override to the new one? (y/n) ',
            false
        );
        while (false === $questionHelper->ask($input, $output, $question)) {
            $output->writeln('<error>You must now copy all required changes from the old override to the new one</error>');
        }
        $output->writeln('<comment>Now updating override</comment>');
        $this->fileOverrider->updateOverrideFiles();
        $output->writeln("<info>\n\nCompleted override update for $relativePathToFileInOverrides\n</info>");
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

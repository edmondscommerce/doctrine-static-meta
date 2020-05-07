<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEmbeddableAction;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEmbeddableSkeletonCommand extends AbstractCommand
{
    public const OPT_NEW_EMBEDDABLE_CATEGORY_NAME       = 'catname';
    public const OPT_NEW_EMBEDDABLE_CATEGORY_NAME_SHORT = 'm';

    public const OPT_NEW_EMBEDDABLE_NAME       = 'name';
    public const OPT_NEW_EMBEDDABLE_NAME_SHORT = 'b';

    /**
     * @var CreateEmbeddableAction
     */
    private CreateEmbeddableAction $action;

    public function __construct(
        CreateEmbeddableAction $action,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->action = $action;
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
                '<comment>Generating new Embeddable  '
                . $input->getOption(static::OPT_NEW_EMBEDDABLE_CATEGORY_NAME) . '\\' .
                $input->getOption(static::OPT_NEW_EMBEDDABLE_NAME)
                . '</comment>'
            );
            $this->checkOptions($input);
            $this->action
                ->setProjectRootDirectory($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH))
                ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE))
                ->setCatName($input->getOption(static::OPT_NEW_EMBEDDABLE_CATEGORY_NAME))
                ->setName($input->getOption(static::OPT_NEW_EMBEDDABLE_NAME))
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
                ->setName(AbstractCommand::COMMAND_PREFIX . 'generate:skeleton-embeddable')
                ->setDefinition(
                    [
                        new InputOption(
                            self::OPT_NEW_EMBEDDABLE_CATEGORY_NAME,
                            self::OPT_NEW_EMBEDDABLE_CATEGORY_NAME_SHORT,
                            InputOption::VALUE_REQUIRED,
                            'The category name for this embeddable, eg Financial'
                        ),
                        new InputOption(
                            self::OPT_NEW_EMBEDDABLE_NAME,
                            self::OPT_NEW_EMBEDDABLE_NAME_SHORT,
                            InputOption::VALUE_REQUIRED,
                            'The name for this embeddable, eg Price'
                        ),
                        $this->getProjectRootPathOption(),
                        $this->getProjectRootNamespaceOption(),
                    ]
                )->setDescription(
                    'Generate an Embeddable skeleton ready to be customised'
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

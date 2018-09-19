<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityCommand extends AbstractCommand
{

    public const OPT_FQN        = 'entity-fully-qualified-name';
    public const OPT_FQN_SHORT  = 'f';
    public const DEFINITION_FQN = 'The fully qualified name of the entity you want to create';

    public const OPT_INT_PRIMARY_KEY       = 'int-primary-key';
    public const OPT_INT_PRIMARY_KEY_SHORT = 'd';
    public const DEFINITION_UUID           = 'Use an Integer primary key in place of the standard UUID primary key';

    public const OPT_ENTITY_SPECIFIC_SAVER        = 'entity-specific-saver';
    public const OPT_ENTITY_SPECIFIC_SAVER_SHORT  = 'c';
    public const DEFINITION_ENTITY_SPECIFIC_SAVER = 'Generate an implmentation of SaverInterface just for this entity';

    /**
     * @var EntityGenerator
     */
    protected $entityGenerator;

    /**
     * GenerateEntityCommand constructor.
     *
     * @param EntityGenerator $relationsGenerator
     * @param null|string     $name
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function __construct(
        EntityGenerator $relationsGenerator,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->entityGenerator = $relationsGenerator;
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function configure(): void
    {
        try {
            $this
                ->setName(AbstractCommand::COMMAND_PREFIX . 'generate:entity')
                ->setDefinition(
                    [
                        new InputOption(
                            self::OPT_FQN,
                            self::OPT_FQN_SHORT,
                            InputOption::VALUE_REQUIRED,
                            self::DEFINITION_FQN
                        ),
                        new InputOption(
                            self::OPT_INT_PRIMARY_KEY,
                            self::OPT_INT_PRIMARY_KEY_SHORT,
                            InputOption::VALUE_NONE,
                            self::DEFINITION_UUID
                        ),
                        new InputOption(
                            self::OPT_ENTITY_SPECIFIC_SAVER,
                            self::OPT_ENTITY_SPECIFIC_SAVER_SHORT,
                            InputOption::VALUE_NONE,
                            self::DEFINITION_ENTITY_SPECIFIC_SAVER
                        ),
                        $this->getProjectRootPathOption(),
                        $this->getProjectRootNamespaceOption(),
                        $this->getSrcSubfolderOption(),
                        $this->getTestSubFolderOption(),
                    ]
                )->setDescription(
                    'Generate an Entity'
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
     * @throws DoctrineStaticMetaException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->checkOptions($input);
            $output->writeln(
                '<comment>Starting generation for ' . $input->getOption(self::OPT_FQN) . '</comment>'
            );
            $idType =
                (true !== $input->getOption(self::OPT_INT_PRIMARY_KEY)) ?
                    IdTrait::UUID_FIELD_TRAIT : IdTrait::INTEGER_ID_FIELD_TRAIT;
            $this->entityGenerator
                ->setPathToProjectRoot($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH))
                ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE))
                ->setTestSubFolderName($input->getOption(AbstractCommand::OPT_TEST_SUBFOLDER))
                ->setPrimaryKeyType($idType);
            $this->entityGenerator->generateEntity(
                $input->getOption(self::OPT_FQN),
                $input->getOption(self::OPT_ENTITY_SPECIFIC_SAVER)
            );
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

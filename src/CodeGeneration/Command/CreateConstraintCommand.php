<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateConstraintCommand extends AbstractCommand
{
    public const OPT_CONSTRAINT_SHORT_NAME       = 'constraint-short-name';
    public const OPT_CONSTRAINT_SHORT_NAME_SHORT = 'c';

    public const DEFINITION_CONSTRAINT_SHORT_NAME =
        'The short basename of the Constraint you want ot create. ' .
        'It will then generate both the Constrain and ConstraintValidator objects as required';

    public const OPT_PROPERTY_OR_ENTITY        = 'property-or-entity';
    public const OPT_PROPERTY_OR_ENTITY_SHORT  = 't';
    public const DEFINITION_PROPERTY_OR_ENTITY =
        'Is this a constraint on a property or the Entity as a whole? (property|entity)';
    public const DEFAULT_PROPERTY_OR_ENTITY    = CreateConstraintAction::OPTION_PROPERTY;

    /**
     * @var CreateConstraintAction
     */
    protected $action;

    public function __construct(CreateConstraintAction $action, ?string $name = null)
    {
        parent::__construct($name);
        $this->action = $action;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln(
                '<comment>Starting generation for '
                . $input->getOption(self::OPT_CONSTRAINT_SHORT_NAME) . '</comment>'
            );
            $this->checkOptions($input);
            $this->action->setProjectRootNamespace($input->getOption(self::OPT_PROJECT_ROOT_NAMESPACE))
                         ->setProjectRootDirectory($input->getOption(self::OPT_PROJECT_ROOT_PATH))
                         ->setPropertyOrEntity($input->getOption(self::OPT_PROPERTY_OR_ENTITY))
                         ->setConstraintShortName($input->getOption(self::OPT_CONSTRAINT_SHORT_NAME))
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
                ->setName(AbstractCommand::COMMAND_PREFIX . 'generate:constraint')
                ->setDefinition(
                    [
                        new InputOption(
                            self::OPT_CONSTRAINT_SHORT_NAME,
                            self::OPT_CONSTRAINT_SHORT_NAME_SHORT,
                            InputOption::VALUE_REQUIRED,
                            self::DEFINITION_CONSTRAINT_SHORT_NAME
                        ),
                        new InputOption(
                            self::OPT_PROPERTY_OR_ENTITY,
                            self::OPT_PROPERTY_OR_ENTITY_SHORT,
                            InputOption::VALUE_OPTIONAL,
                            self::DEFINITION_PROPERTY_OR_ENTITY,
                            self::DEFAULT_PROPERTY_OR_ENTITY
                        ),
                        $this->getProjectRootPathOption(),
                        $this->getProjectRootNamespaceOption(),
                    ]
                )->setDescription(
                    'Generate a a custom constraint'
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

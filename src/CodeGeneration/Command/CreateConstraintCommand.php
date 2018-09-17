<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints\ConstraintCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Creation\Src\Validation\Constraints\ConstraintValidatorCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateConstraintCommand extends AbstractCommand
{
    public const OPT_CONSTRAINT_SHORT_NAME        = 'constraint-short-name';
    public const OPT_CONSTRAINT_SHORT_NAME_SHORT  = 'c';
    public const DEFINITION_CONSTRAINT_SHORT_NAME =
        'The short basename of the Constraint you want ot create. ' .
        'It will then generate both the Constrain and ConstraintValidator objects as required';
    /**
     * @var ConstraintCreator
     */
    protected $constraintCreator;
    /**
     * @var ConstraintValidatorCreator
     */
    protected $constraintValidatorCreator;

    public function __construct(ConstraintCreator $constraintCreator, ConstraintValidatorCreator $constraintValidatorCreator,NamespaceHelper $namespaceHelper, ?string $name = null)
    {
        parent::__construct($namespaceHelper, $name);
        $this->constraintCreator = $constraintCreator;
        $this->constraintValidatorCreator = $constraintValidatorCreator;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->checkOptions($input);
            $output->writeln(
                '<comment>Starting generation for ' . $input->getOption(self::OPT_CONSTRAINT_SHORT_NAME) . '</comment>'
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
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetRelationCommand extends AbstractCommand
{
    public const OPT_ENTITY1       = 'entity1';
    public const OPT_ENTITY1_SHORT = 'm';

    public const OPT_HAS_TYPE       = 'hasType';
    public const OPT_HAS_TYPE_SHORT = 't';

    public const OPT_ENTITY2       = 'entity2';
    public const OPT_ENTITY2_SHORT = 'i';

    public const OPT_REQUIRED_RELATION       = 'required-relation';
    public const OPT_REQUIRED_RELATION_SHORT = 'w';
    public const DEFAULT_REQUIRED_RELATION   = false;

    /**
     * @var RelationsGenerator
     */
    protected $relationsGenerator;

    /**
     * SetRelationCommand constructor.
     *
     * @param RelationsGenerator $relationsGenerator
     * @param null|string        $name
     *
     * @throws DoctrineStaticMetaException
     */
    public function __construct(
        RelationsGenerator $relationsGenerator,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->relationsGenerator = $relationsGenerator;
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    public function configure(): void
    {
        try {
            $this->setName(AbstractCommand::COMMAND_PREFIX . 'set:relation')
                 ->setDefinition(
                     [
                         new InputOption(
                             self::OPT_ENTITY1,
                             self::OPT_ENTITY1_SHORT,
                             InputOption::VALUE_REQUIRED,
                             'First entity in relation'
                         ),
                         new InputOption(
                             self::OPT_HAS_TYPE,
                             self::OPT_HAS_TYPE_SHORT,
                             InputOption::VALUE_REQUIRED,
                             'What type of relation is it? '
                             . 'Must be one of ' . RelationsGenerator::class . '::RELATION_TYPES'
                         ),
                         new InputOption(
                             self::OPT_ENTITY2,
                             self::OPT_ENTITY2_SHORT,
                             InputOption::VALUE_REQUIRED,
                             'Second entity in relation'
                         ),
                         new InputOption(
                             self::OPT_REQUIRED_RELATION,
                             self::OPT_REQUIRED_RELATION_SHORT,
                             InputOption::VALUE_OPTIONAL,
                             'Is the relation required (i.e. not nullable)?',
                             self::DEFAULT_REQUIRED_RELATION
                         ),
                         $this->getProjectRootPathOption(),
                         $this->getProjectRootNamespaceOption(),
                         $this->getSrcSubfolderOption(),
                     ]
                 )->setDescription(
                    'Set a relation between 2 entities. The relation must be one of '
                    . RelationsGenerator::class . '::RELATION_TYPES'
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
     * @return int|null|void
     * @throws DoctrineStaticMetaException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln(
                '<comment>Setting relation: '
                . $input->getOption(static::OPT_ENTITY1)
                . ' ' . $input->getOption(static::OPT_HAS_TYPE)
                . ' ' . $input->getOption(static::OPT_ENTITY2)
                . '</comment>'
            );
            $this->checkOptions($input);
            $hasType = $input->getOption(static::OPT_HAS_TYPE);
            if (!\in_array($hasType, RelationsGenerator::HAS_TYPES, true)) {
                $hasType = RelationsGenerator::PREFIX_OWNING . $hasType;
                if (!\in_array($hasType, RelationsGenerator::HAS_TYPES, true)) {
                    throw new DoctrineStaticMetaException(
                        'Invalid hasType ' . $input->getOption(static::OPT_HAS_TYPE)
                        . ' Must be one of ' . print_r(RelationsGenerator::HAS_TYPES, true)
                    );
                }
            }
            $this->relationsGenerator
                ->setPathToProjectRoot($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH))
                ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE));

            $this->relationsGenerator->setEntityHasRelationToEntity(
                $input->getOption(static::OPT_ENTITY1),
                $hasType,
                $input->getOption(static::OPT_ENTITY2),
                $input->getOption(self::OPT_REQUIRED_RELATION)
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

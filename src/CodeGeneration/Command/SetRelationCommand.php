<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Factory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetRelationCommand extends AbstractCommand
{
    const OPT_ENTITY1       = 'entity1';
    const OPT_ENTITY1_SHORT = 'm';

    const OPT_HAS_TYPE       = 'hasType';
    const OPT_HAS_TYPE_SHORT = 't';

    const OPT_ENTITY2       = 'entity2';
    const OPT_ENTITY2_SHORT = 'i';

    /**
     * @var RelationsGenerator
     */
    protected $relationsGenerator;

    public function __construct(?string $name = null, RelationsGenerator $entityGenerator)
    {
        parent::__construct($name);
        $this->relationsGenerator = $entityGenerator;
    }

    public function configure()
    {
        $this->setName(AbstractCommand::COMMAND_PREFIX.'set:relation')
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
                        'What type of relation is it? Must be one of \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::RELATION_TYPES'
                    ),
                    new InputOption(
                        self::OPT_ENTITY2,
                        self::OPT_ENTITY2_SHORT,
                        InputOption::VALUE_REQUIRED,
                        'Second entity in relation'
                    ),
                     $this->getProjectRootPathOption(),
                     $this->getProjectRootNamespaceOption(),
                     $this->getProjectEntitiesRootNamespaceOption(),
                     $this->getSrcSubfolderOption(),
                 ]
            )->setDescription(
                'Set a relation between 2 entities. The relation must be one of \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::RELATION_TYPES'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            '<comment>Setting relation: '
            .$input->getOption(static::OPT_ENTITY1)
            .' '.$input->getOption(static::OPT_HAS_TYPE)
            .' '.$input->getOption(static::OPT_ENTITY2)
            .'</comment>'
        );
        $this->checkOptions($input);
        $hasType = $input->getOption(static::OPT_HAS_TYPE);
        if (!in_array($hasType, RelationsGenerator::HAS_TYPES, true)) {
            $hasType = RelationsGenerator::PREFIX_OWNING.$hasType;
            if (!in_array($hasType, RelationsGenerator::HAS_TYPES, true)) {
                throw new DoctrineStaticMetaException(
                    'Invalid hasType '.$input->getOption(static::OPT_HAS_TYPE)
                    .' Must be one of '.print_r(RelationsGenerator::HAS_TYPES, true)
                );
            }
        }
        $this->relationsGenerator
            ->setPathToProjectSrcRoot($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH))
            ->setEntitiesFolderName($input->getOption(AbstractCommand::OPT_ENTITIES_ROOT_FOLDER))
            ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE));

        $this->relationsGenerator->setEntityHasRelationToEntity(
            $input->getOption(static::OPT_ENTITY1),
            $hasType,
            $input->getOption(static::OPT_ENTITY2)
        );
        $output->writeln('<info>completed</info>');
    }
}

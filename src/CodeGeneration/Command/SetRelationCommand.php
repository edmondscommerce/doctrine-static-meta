<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetRelationCommand extends AbstractCommand
{
    const OPT_ENTITY1 = 'entity1';
    const OPT_ENTITY1_SHORT = 'e1';

    const OPT_RELATION_TYPE = 'type';
    const OPT_RELATION_TYPE_SHORT = 't';

    const OPT_ENTITY2 = 'entity2';
    const OPT_ENTITY2_SHORT = 'e2';

    public function configure()
    {
        $this->setName(AbstractCommand::COMMAND_PREFIX . 'set:relation')
            ->setDefinition(
                array(
                    new InputOption(
                        AbstractCommand::OPT_PROJECT_ROOT_PATH,
                        AbstractCommand::OPT_PROJECT_ROOT_PATH_SHORT,
                        InputOption::VALUE_REQUIRED,
                        AbstractCommand::DEFINITION_PROJECT_ROOT_PATH
                    ),
                    new InputOption(
                        AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE,
                        AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT,
                        InputOption::VALUE_REQUIRED,
                        AbstractCommand::DEFINITION_PROJECT_ROOT_NAMESPACE
                    ),
                    new InputOption(
                        AbstractCommand::OPT_ENTITIES_ROOT_NAMESPACE,
                        AbstractCommand::OPT_ENTITIES_ROOT_NAMESPACE_SHORT,
                        InputOption::VALUE_OPTIONAL,
                        AbstractCommand::DEFINITION_ENTITIES_ROOT_NAMESPACE,
                        AbstractCommand::DEFINITION_ENTITIES_ROOT_NAMESPACE
                    ),
                    new InputOption(
                        self::OPT_ENTITY1,
                        self::OPT_ENTITY1_SHORT,
                        InputOption::VALUE_REQUIRED,
                        'First entity in relation'
                    ),
                    new InputOption(
                        self::OPT_RELATION_TYPE,
                        self::OPT_RELATION_TYPE_SHORT,
                        InputOption::VALUE_REQUIRED,
                        'What type of relation is it? Must be one of \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::RELATION_TYPES'

                    ),
                    new InputOption(
                        self::OPT_ENTITY2,
                        self::OPT_ENTITY2_SHORT,
                        InputOption::VALUE_REQUIRED,
                        'Second entity in relation'
                    ),
                )
            )->setDescription(
                'Set a relatoin between 2 entities. The relation must be one of \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::RELATION_TYPES'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkAllRequiredOptionsAreNotEmpty($input);
        $relationsGenerator = new RelationsGenerator(
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE),
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH)
        );
        $relationsGenerator->setEntityHasRelationToEntity(
            $input->getOption(static::OPT_ENTITY1),
            $input->getOption(static::OPT_RELATION_TYPE),
            $input->getOption(static::OPT_ENTITY2)
        );
    }

}

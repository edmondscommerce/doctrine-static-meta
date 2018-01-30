<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetRelationCommand extends AbstractCommand
{
    const OPT_ENTITY1       = 'entity1';
    const OPT_ENTITY1_SHORT = 'e1';

    const OPT_HAS_TYPE       = 'hasType';
    const OPT_HAS_TYPE_SHORT = 'ht';

    const OPT_ENTITY2       = 'entity2';
    const OPT_ENTITY2_SHORT = 'e2';

    public function configure()
    {
        $this->setName(AbstractCommand::COMMAND_PREFIX . 'set:relation')
             ->setDefinition(
                 [
                     $this->getProjectRootPathOption(),
                     $this->getProjectRootNamespaceOption(),
                     $this->getProjectEntitiesRootNamespaceOption(),
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
                 ]
             )->setDescription(
                'Set a relatoin between 2 entities. The relation must be one of \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::RELATION_TYPES'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            '<comment>Setting relation: '
            . $input->getOption(static::OPT_ENTITY1)
            . ' ' . $input->getOption(static::OPT_HAS_TYPE)
            . ' ' . $input->getOption(static::OPT_ENTITY2)
            . '</comment>'
        );
        $this->checkAllRequiredOptionsAreNotEmpty($input);
        $relationsGenerator = new RelationsGenerator(
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE),
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH)
        );
        $relationsGenerator->setEntityHasRelationToEntity(
            $input->getOption(static::OPT_ENTITY1),
            $input->getOption(static::OPT_HAS_TYPE),
            $input->getOption(static::OPT_ENTITY2)
        );
        $output->writeln('<info>completed</info>');
    }

}

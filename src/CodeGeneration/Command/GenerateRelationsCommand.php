<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRelationsCommand extends AbstractCommand
{

    const OPT_FILTER       = 'filter';
    const OPT_FILTER_SHORT = 'f';


    protected function configure()
    {
        $this
            ->setName(AbstractCommand::COMMAND_PREFIX . 'generate:relations')
            ->setDefinition(
                [
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
                        self::OPT_FILTER,
                        self::OPT_FILTER_SHORT,
                        InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                        'A string pattern used to match entities that should be processed.'
                    ),
                ]
            )->setDescription(
                'Generate relations traits for your entities. Optionally filter down the list of entities to generate relationship traits for'
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkAllRequiredOptionsAreNotEmpty($input);
        $em  = $this->getEntityManager();
        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);
        /**
         * @var ClassMetadata[] $metadatas
         */
        $metadatas          = $cmf->getAllMetadata();
        $metadatas          = MetadataFilter::filter($metadatas, $input->getOption('filter'));
        $relationsGenerator = new RelationsGenerator(
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE),
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH)
        );

        $output->writeln('<comment>Starting relations generation for ' . implode(' ', $input->getOption('filter')) . '</comment>');
        $progress = new ProgressBar($output, count($metadatas));
        $progress->setFormatDefinition('custom', ' %current%/%max% -- %message%');
        foreach ($metadatas as $metadata) {
            $progress->setMessage('<comment>Generating for ' . $metadata->name . '</comment>');
            $relationsGenerator->generateRelationTraitsForEntity($metadata->name);
            $progress->setMessage('<info>done</info>');
            $progress->advance();
        }
        $progress->finish();
        $output->writeln('completed');

    }
}

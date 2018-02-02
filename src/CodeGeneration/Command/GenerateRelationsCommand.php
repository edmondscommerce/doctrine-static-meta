<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Factory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRelationsCommand extends AbstractCommand
{

    const OPT_FILTER       = 'filter';
    const OPT_FILTER_SHORT = 'f';

    /**
     * @var RelationsGenerator
     */
    protected $relationsGenerator;

    public function __construct(?string $name = null, RelationsGenerator $entityGenerator)
    {
        parent::__construct($name);
        $this->relationsGenerator = $entityGenerator;
    }


    protected function configure()
    {
        $this
            ->setName(AbstractCommand::COMMAND_PREFIX.'generate:relations')
            ->setDefinition(
                [
                    new InputOption(
                        self::OPT_FILTER,
                        self::OPT_FILTER_SHORT,
                        InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                        'A string pattern used to match entities that should be processed.'
                    ),
                    $this->getProjectRootPathOption(),
                    $this->getProjectRootNamespaceOption(),
                    $this->getProjectEntitiesRootNamespaceOption(),
                    $this->getSrcSubfolderOption(),
                ]
            )->setDescription(
                'Generate relations traits for your entities. Optionally filter down the list of entities to generate relationship traits for'
            );
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @suppressWarnings(PHPMD)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkOptions($input);
        $entityManager = $this->getEntityManager();
        $cmf           = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($entityManager);
        /**
         * @var ClassMetadata[] $metadatas
         */
        $metadatas = $cmf->getAllMetadata();
        $metadatas = MetadataFilter::filter($metadatas, $input->getOption('filter'));
        $this->relationsGenerator
            ->setPathToProjectSrcRoot($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH))
            ->setEntitiesFolderName($input->getOption(AbstractCommand::OPT_ENTITIES_ROOT_FOLDER))
            ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE));

        $output->writeln(
            '<comment>Starting relations generation for '
            .implode(' ',
                     $input->getOption('filter')
            ).'</comment>'
        );
        $progress = new ProgressBar($output, count($metadatas));
        $progress->setFormatDefinition('custom', ' %current%/%max% -- %message%');
        $progress->start();
        foreach ($metadatas as $metadata) {
            $progress->setMessage('<comment>Generating for '.$metadata->name.'</comment>');
            $this->relationsGenerator->generateRelationCodeForEntity($metadata->name);
            $progress->setMessage('<info>done</info>');
            $progress->advance();
        }
        $progress->finish();
        $output->writeln('completed');
    }
}

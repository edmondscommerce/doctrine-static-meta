<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRelationsCommand extends AbstractCommand
{

    public const OPT_FILTER       = 'filter';
    public const OPT_FILTER_SHORT = 'f';

    /**
     * @var RelationsGenerator
     */
    protected $relationsGenerator;

    /**
     * GenerateRelationsCommand constructor.
     *
     * @param RelationsGenerator $entityGenerator
     * @param NamespaceHelper    $namespaceHelper
     * @param null|string        $name
     *
     * @throws DoctrineStaticMetaException
     */
    public function __construct(
        RelationsGenerator $entityGenerator,
        NamespaceHelper $namespaceHelper,
        ?string $name = null
    ) {
        parent::__construct($namespaceHelper, $name);
        $this->relationsGenerator = $entityGenerator;
    }


    /**
     * @throws DoctrineStaticMetaException
     */
    protected function configure(): void
    {
        try {
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
                        $this->getProjectEntitiesRootFolderOption(),
                        $this->getProjectEntityRelationsRootFolderOption(),
                        $this->getSrcSubfolderOption(),
                    ]
                )->setDescription(
                    'Generate relations traits for your entities. '
                    .'Optionally filter down the list of entities to generate relationship traits for'
                );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD)
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
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
                ->setEntityRelationsFolderName(
                    $input->getOption(AbstractCommand::OPT_ENTITY_RELATIONS_ROOT_FOLDER)
                )
                ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE));

            $output->writeln(
                '<comment>Starting relations generation for '
                .implode(
                    ' ',
                    $input->getOption('filter')
                ).'</comment>'
            );
            $progress = new ProgressBar($output, count($metadatas));
            $progress::setFormatDefinition('custom', ' %current%/%max% -- %message%');
            $progress->start();
            foreach ($metadatas as $metadata) {
                $progress->setMessage('<comment>Generating for '.$metadata->name.'</comment>');
                $this->relationsGenerator->generateRelationCodeForEntity($metadata->name);
                $progress->setMessage('<info>done</info>');
                $progress->advance();
            }
            $progress->finish();
            $output->writeln('completed');
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}

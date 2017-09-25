<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRelationsCommand extends AbstractCommand
{

    const OPT_FILTER = 'filter';
    const ARG_PATH = 'path';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_PREFIX.'generate-relations')
            ->setDefinition(
                array(
                    new InputOption(
                        self::OPT_FILTER,
                        null,
                        InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                        'A string pattern used to match entities that should be processed.'
                    ),
                    new InputArgument(
                        self::ARG_PATH,
                        InputArgument::REQUIRED,
                        'path to the entitites root'
                    ),
                )
            );

    }

    protected function getEm(): EntityManager
    {
        $em = $this->getHelper('em')->getEntityManager();

        return $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEm();
        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);
        $metadatas = $cmf->getAllMetadata();
        $metadatas = MetadataFilter::filter($metadatas, $input->getOption('filter'));
        $traitsPath = $input->getArgument(self::ARG_PATH).'/Traits';
        mkdir($traitsPath, 0777, true);
        foreach ($metadatas as $metadata) {

        }

    }
}
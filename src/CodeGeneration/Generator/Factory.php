<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;


use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;

class Factory
{
    public static function getRelationsGeneratorUsingInput(InputInterface $input): RelationsGenerator
    {
        return new RelationsGenerator(
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE),
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH),
            $input->getOption(AbstractCommand::OPT_ENTITIES_ROOT_FOLDER),
            $input->getOption(AbstractCommand::OPT_SRC_SUBFOLDER)
        );
    }

    public static function getEntityGeneratorUsingInput(InputInterface $input): EntityGenerator
    {
        return new EntityGenerator(
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE),
            $input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH),
            $input->getOption(AbstractCommand::OPT_ENTITIES_ROOT_FOLDER),
            $input->getOption(AbstractCommand::OPT_SRC_SUBFOLDER),
            $input->getOption(AbstractCommand::OPT_TEST_SUBFOLDER)
        );
    }
}

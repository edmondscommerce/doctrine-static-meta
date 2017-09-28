<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityCommand extends AbstractCommand
{

    const ARG_FQN = 'entity-fully-qualified-name';
    const ARG_FQN_SHORT = 'fqn';
    const DEF_FQN = 'The fully qualified name of the entity you want to create';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_PREFIX.'generate:entity')
            ->setDefinition(
                array(
                    new InputOption(
                        self::ARG_PROJECT_ROOT_PATH,
                        self::ARG_PROJECT_ROOT_PATH_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEF_PROJECT_ROOT_PATH
                    ),
                    new InputOption(
                        self::ARG_PROJECT_ROOT_NAMESPACE,
                        self::ARG_PROJECT_ROOT_NAMESPACE_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEF_PROJECT_ROOT_NAMESPACE
                    ),
                    new InputOption(
                        self::ARG_FQN,
                        self::ARG_FQN_SHORT,
                        InputOption::VALUE_REQUIRED,
                        self::DEF_FQN
                    ),
                )
            );

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fqn = $input->getOption(self::ARG_FQN);
        $fqnParts = explode('\\', $fqn);
        $className = array_pop($fqnParts);
        $namespace = implode('\\', $fqnParts);
        $rootParts = explode('\\', $input->getOption(self::ARG_PROJECT_ROOT_NAMESPACE));
        $subParts = array_diff($fqnParts, $rootParts);
        $fs = $this->getFilesystem();
        $path = $input->getOption(self::ARG_PROJECT_ROOT_PATH);
        if (!$fs->exists($path)) {
            throw new \Exception(self::ARG_PROJECT_ROOT_PATH." $path does not exist");
        }
        foreach ($subParts as $sd) {
            $path .= "/$sd";
            $fs->mkdir($path);
        }
        $filePath = "$path/$className.php";
        $fs->copy(self::TEMPLATE_PATH.'/Entities/TemplateEntity.php', $filePath);
        $this->findReplace(self::FIND_ENTITY_NAME, $className, $filePath);
        $this->findReplace(self::FIND_NAMESPACE, $namespace, $filePath);
    }

    protected function findReplace(string $find, string $replace, string $filePath)
    {
        $contents = file_get_contents($filePath);
        $contents = str_replace($find, $replace, $contents);
        file_put_contents($filePath, $contents);
    }
}
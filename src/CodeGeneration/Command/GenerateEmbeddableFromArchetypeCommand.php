<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEmbeddableFromArchetypeCommand extends AbstractCommand
{
    public const OPT_NEW_EMBEDDABLE_CLASS_NAME       = 'classname';
    public const OPT_NEW_EMBEDDABLE_CLASS_NAME_SHORT = 'c';

    public const OPT_ARCHETYPE_OBJECT_FQN       = 'archetype';
    public const OPT_ARCHETYPE_OBJECT_FQN_SHORT = 'a';
    /**
     * @var ArchetypeEmbeddableGenerator
     */
    protected $embeddableGenerator;

    public function __construct(
        ArchetypeEmbeddableGenerator $embeddableGenerator,
        NamespaceHelper $namespaceHelper,
        ?string $name = null
    ) {
        parent::__construct($namespaceHelper, $name);
        $this->embeddableGenerator = $embeddableGenerator;
    }


    /**
     * @throws DoctrineStaticMetaException
     */
    protected function configure(): void
    {
        try {
            $this
                ->setName(AbstractCommand::COMMAND_PREFIX.'generate:field')
                ->setDefinition(
                    [
                        new InputOption(
                            self::OPT_NEW_EMBEDDABLE_CLASS_NAME,
                            self::OPT_NEW_EMBEDDABLE_CLASS_NAME_SHORT,
                            InputOption::VALUE_REQUIRED,
                            'The short class name for the new Embeddable Object'
                        ),
                        new InputOption(
                            self::OPT_ARCHETYPE_OBJECT_FQN,
                            self::OPT_ARCHETYPE_OBJECT_FQN_SHORT,
                            InputOption::VALUE_REQUIRED,
                            'The fully qualified name for the archetype Embeddable Object'
                        ),
                        $this->getProjectRootPathOption(),
                        $this->getProjectRootNamespaceOption(),
                    ]
                )->setDescription(
                    'Generate an Embeddable from an Archetype Embeddable'
                );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e
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
                '<comment>Generating new Embeddable  '
                .$input->getOption(static::OPT_NEW_EMBEDDABLE_CLASS_NAME)
                .' from archetype '.$input->getOption(static::OPT_ARCHETYPE_OBJECT_FQN)
                .'</comment>'
            );
            $this->checkOptions($input);
            $this->embeddableGenerator
                ->setPathToProjectRoot($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_PATH))
                ->setProjectRootNamespace($input->getOption(AbstractCommand::OPT_PROJECT_ROOT_NAMESPACE))
                ->createFromArchetype(
                    $input->getOption(static::OPT_ARCHETYPE_OBJECT_FQN),
                    $input->getOption(static::OPT_NEW_EMBEDDABLE_CLASS_NAME)
                );
            $output->writeln('<info>completed</info>');
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e
            );
        }
    }
}

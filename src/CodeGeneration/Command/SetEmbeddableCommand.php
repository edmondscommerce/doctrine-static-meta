<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetEmbeddableCommand extends AbstractCommand
{
    public const OPT_ENTITY       = 'entity';
    public const OPT_ENTITY_SHORT = 'o';

    public const OPT_EMBEDDABLE       = 'embeddable';
    public const OPT_EMBEDDABLE_SHORT = 'b';
    /**
     * @var EntityEmbeddableSetter
     */
    protected $embeddableSetter;

    public function __construct(
        EntityEmbeddableSetter $embeddableSetter,
        NamespaceHelper $namespaceHelper,
        ?string $name = null
    ) {
        parent::__construct($namespaceHelper, $name);
        $this->embeddableSetter = $embeddableSetter;
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    public function configure(): void
    {
        try {
            $this->setName(AbstractCommand::COMMAND_PREFIX . 'set:embeddable')
                 ->setDefinition(
                     [
                         new InputOption(
                             self::OPT_ENTITY,
                             self::OPT_ENTITY_SHORT,
                             InputOption::VALUE_REQUIRED,
                             'Entity Fully Qualified Name'
                         ),
                         new InputOption(
                             self::OPT_EMBEDDABLE,
                             self::OPT_EMBEDDABLE_SHORT,
                             InputOption::VALUE_REQUIRED,
                             'Embeddable Trait Fully Qualified Name'
                         ),
                     ]
                 )->setDescription(
                     'Set an Entity as having an Embeddable by way of using the Embeddable Trait'
                 );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
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
                '<comment>Setting Entity '
                . $input->getOption(static::OPT_ENTITY)
                . ' has Embeddable ' . $input->getOption(static::OPT_EMBEDDABLE)
                . '</comment>'
            );
            $this->checkOptions($input);
            $this->embeddableSetter
                ->setEntityHasEmbeddable(
                    $input->getOption(static::OPT_ENTITY),
                    $input->getOption(static::OPT_EMBEDDABLE)
                );
            $output->writeln('<info>completed</info>');
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}

<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetFieldCommand extends AbstractCommand
{
    public const OPT_ENTITY       = 'entity';
    public const OPT_ENTITY_SHORT = 'o';

    public const OPT_FIELD       = 'field';
    public const OPT_FIELD_SHORT = 't';
    /**
     * @var \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter
     */
    protected $entityFieldSetter;

    /**
     * SetFieldCommand constructor.
     *
     * @param \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter $entityFieldSetter
     * @param NamespaceHelper                                                                      $namespaceHelper
     * @param null|string                                                                          $name
     *
     * @throws DoctrineStaticMetaException
     */
    public function __construct(
        EntityFieldSetter $entityFieldSetter,
        NamespaceHelper $namespaceHelper,
        ?string $name = null
    ) {
        parent::__construct($namespaceHelper, $name);
        $this->entityFieldSetter = $entityFieldSetter;
    }


    /**
     * @throws DoctrineStaticMetaException
     */
    public function configure(): void
    {
        try {
            $this->setName(AbstractCommand::COMMAND_PREFIX . 'set:field')
                 ->setDefinition(
                     [
                         new InputOption(
                             self::OPT_ENTITY,
                             self::OPT_ENTITY_SHORT,
                             InputOption::VALUE_REQUIRED,
                             'Entity Fully Qualified Name'
                         ),
                         new InputOption(
                             self::OPT_FIELD,
                             self::OPT_FIELD_SHORT,
                             InputOption::VALUE_REQUIRED,
                             'Field Fully Qualified Name'
                         ),
                         $this->getProjectRootPathOption(),
                         $this->getProjectRootNamespaceOption(),
                         $this->getSrcSubfolderOption(),
                     ]
                 )->setDescription(
                     'Set an Entity as having a Field'
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
                . ' has Field ' . $input->getOption(static::OPT_FIELD)
                . '</comment>'
            );
            $this->checkOptions($input);
            $this->entityFieldSetter
                ->setEntityHasField(
                    $input->getOption(static::OPT_ENTITY),
                    $input->getOption(static::OPT_FIELD)
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

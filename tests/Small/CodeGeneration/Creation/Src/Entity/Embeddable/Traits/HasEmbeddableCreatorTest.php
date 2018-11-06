<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Embeddable\Traits;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Embeddable\Traits\HasEmbeddableTraitCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use PHPUnit\Framework\TestCase;

class HasEmbeddableCreatorTest extends TestCase
{
    private const EXPECTED_FILE = <<<'PHP'
<?php declare(strict_types=1);

namespace Test\Project\Entity\Embeddable\Traits\Foo;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Test\Project\Entity\Embeddable\Interfaces\Foo\HasBarEmbeddableInterface;
use Test\Project\Entity\Embeddable\Interfaces\Objects\Foo\BarEmbeddableInterface;
use Test\Project\Entity\Embeddable\Objects\Foo\BarEmbeddable;

trait HasBarEmbeddableTrait
{
    /**
     * @var BarEmbeddableInterface
     */
    private $barEmbeddable;

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForBarEmbeddable(ClassMetadataBuilder $builder): void
    {
        $builder->addLifecycleEvent(
            'postLoadSetOwningEntityOnBarEmbeddable',
            Events::postLoad
        );
        $builder->createEmbedded(
            HasBarEmbeddableInterface::PROP_BAR_EMBEDDABLE,
            BarEmbeddable::class
        )
                ->setColumnPrefix(
                    HasBarEmbeddableInterface::COLUMN_PREFIX_BAR
                )
                ->build();
    }

    /**
     * @return mixed
     */
    public function getBarEmbeddable(): BarEmbeddableInterface
    {
        return $this->barEmbeddable;
    }

    public function postLoadSetOwningEntityOnBarEmbeddable(): void
    {
        $this->barEmbeddable->setOwningEntity($this);
    }

    /**
     * Called at construction time
     */
    private function initBarEmbeddable(): void
    {
        $this->setBarEmbeddable(
            new BarEmbeddable(
                BarEmbeddableInterface::DEFAULT_PROPERTY_ONE,
                BarEmbeddableInterface::DEFAULT_PROPERTY_TWO
            ),
            false
        );
    }

    /**
     * @param BarEmbeddable $barEmbeddable
     *
     * @param bool               $notify
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function setBarEmbeddable(BarEmbeddable $barEmbeddable, bool $notify = true): self
    {
        $this->barEmbeddable = $barEmbeddable;
        $this->barEmbeddable->setOwningEntity($this);
        if (true === $notify) {
            $this->notifyEmbeddablePrefixedProperties(
                HasBarEmbeddableInterface::PROP_BAR_EMBEDDABLE
            );
        }

        return $this;
    }
}
PHP;

    /**
     * @test
     */
    public function itCanCreateTheFile(): void
    {
        $file     = $this->getCreator()
                         ->setCatName('Foo')
                         ->setName('Bar')
                         ->createTargetFileObject()
                         ->getTargetFile();
        $expected = self::EXPECTED_FILE;
        $actual   = $file->getContents();
        self::assertSame($expected, $actual);
    }

    private function getCreator(): HasEmbeddableTraitCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        $creator = new HasEmbeddableTraitCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory()
        );
        $creator->setProjectRootNamespace('Test\Project');

        return $creator;
    }
}
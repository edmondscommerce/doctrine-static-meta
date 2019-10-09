<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Fields\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Interfaces\FieldInterfaceCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @small
 */
class FieldInterfaceCreatorTest extends TestCase
{
    /**
     * @test
     */
    public function itRequiresTheSuffix(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\TestArray';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$newObjectFqn must end in FieldInterface');
        $this->getCreator()->createTargetFileObject($newObjectFqn);
    }

    private function getCreator(): FieldInterfaceCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new FieldInterfaceCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory(),
            new CodeHelper($namespaceHelper),
            new TypeHelper()
        );
    }

    public function provideTypesToDefaultsAndExpectedText(): array
    {
        return [
            MappingHelper::TYPE_STRING         => [MappingHelper::TYPE_STRING, 'foo', "'foo'"],
            MappingHelper::TYPE_BOOLEAN        => [MappingHelper::TYPE_BOOLEAN, true, 'true'],
            MappingHelper::TYPE_FLOAT          => [MappingHelper::TYPE_FLOAT, 2.2, '2.2'],
            MappingHelper::TYPE_FLOAT . '_int' => [MappingHelper::TYPE_FLOAT, 3, '3.0'],
            MappingHelper::TYPE_INTEGER        => [MappingHelper::TYPE_INTEGER, 4, '4'],
        ];
    }

    /**
     * @test
     * @dataProvider provideTypesToDefaultsAndExpectedText
     *
     * @param string $type
     * @param mixed  $default
     * @param string $match
     */
    public function itCanSetDefaultValues(string $type, $default, string $match): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\Test'
                        . ucfirst($type) . 'FieldInterface';
        $contents     = $this->getCreator()
                             ->setMappingHelperCommonType($type)
                             ->setDefaultValue($default)
                             ->createTargetFileObject($newObjectFqn)
                             ->getTargetFile()
                             ->getContents();
        self::assertRegExp('%public const DEFAULT_.+? = ' . $match . '%', $contents);
    }

    /**
     * @test
     */
    public function itCanCreateABooleanFieldInterface(): void
    {
        $contents = $this->itCanCreateAFieldInterface(MappingHelper::TYPE_BOOLEAN);
        self::assertContains('function isTestBoolean(): ?bool', $contents);
    }

    /**
     * @test
     *
     * @param string $type
     *
     * @return string
     */
    public function itCanCreateAFieldInterface(string $type = MappingHelper::PHP_TYPE_ARRAY): string
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\Test'
                        . ucfirst($type) . 'FieldInterface';
        $contents     = $this->getCreator()
                             ->setMappingHelperCommonType($type)
                             ->createTargetFileObject($newObjectFqn)
                             ->getTargetFile()
                             ->getContents();
        self::assertNotEmpty($contents);

        return $contents;
    }

    /**
     * @test
     */
    public function itCreatesStringFieldsWithExtraValidation(): void
    {
        $this->itCanCreateAFieldInterface(MappingHelper::PHP_TYPE_STRING);
    }
}

<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Creation\Src\Entity\Fields\Traits;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Traits\FieldTraitCreator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Small\ConfigTest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @small
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Fields\Traits\FieldTraitCreator
 */
class FieldTraitCreatorTest extends TestCase
{
    private const FIELD_TRAIT = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TestStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use Symfony\Component\Validator\Constraints\Length;                

trait TestStringFieldTrait
{
    /**
     * @var string|null
     */
    private ?string $testString;

    /**
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTestString(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [TestStringFieldInterface::PROP_TEST_STRING],
            $builder,
            TestStringFieldInterface::DEFAULT_TEST_STRING,
            true
        );
    }

    /**
     * This method sets the validation for this field.
     * You should add in as many relevant property constraints as you see fit.
     *
     * Remove the PHPMD suppressed warning once you start setting constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     */
    protected static function validatorMetaForPropertyTestString(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            TestStringFieldInterface::PROP_TEST_STRING,
            new Length(['min' => 0, 'max' => Database::MAX_VARCHAR_LENGTH])
        );
    }

    /**
     * @return string|null
     */
    public function getTestString(): ?string
    {
        if (null === $this->testString) {
            return TestStringFieldInterface::DEFAULT_TEST_STRING;
        }

        return $this->testString;
    }

    private function initTestString(): void
    {
        $this->testString = TestStringFieldInterface::DEFAULT_TEST_STRING;
    }

    /**
     * @param string|null $testString
     *
     * @return self
     */
    private function setTestString(?string $testString): self
    {
        $this->updatePropertyValue(
            TestStringFieldInterface::PROP_TEST_STRING,
            $testString
        );

        return $this;
    }
}
PHP;

    private const DATETIME_FIELD_TRAIT = <<<'PHP'
<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TestDateTimeFieldInterface;

trait TestDateTimeFieldTrait
{
    /**
     * @var \DateTimeImmutable|null
     */
    private ?\DateTimeImmutable $testDateTime;

    /**
     * @param ClassMetadataBuilder $builder
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForTestDateTime(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleDatetimeFields(
            [TestDateTimeFieldInterface::PROP_TEST_DATE_TIME],
            $builder,
            TestDateTimeFieldInterface::DEFAULT_TEST_DATE_TIME
        );
    }

    /**
     * This method sets the validation for this field.
     * You should add in as many relevant property constraints as you see fit.
     *
     * Remove the PHPMD suppressed warning once you start setting constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     */
    protected static function validatorMetaForPropertyTestDateTime(ValidatorClassMetaData $metadata): void
    {
//        $metadata->addPropertyConstraint(
//            TestDateTimeFieldInterface::PROP_TEST_DATE_TIME,
//            new NotBlank()
//        );
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getTestDateTime(): ?\DateTimeImmutable
    {
        if (null === $this->testDateTime) {
            return TestDateTimeFieldInterface::DEFAULT_TEST_DATE_TIME;
        }

        return $this->testDateTime;
    }

    private function initTestDateTime(): void
    {
        $this->testDateTime = TestDateTimeFieldInterface::DEFAULT_TEST_DATE_TIME;
    }

    /**
     * @param \DateTimeImmutable|null $testDateTime
     *
     * @return self
     */
    private function setTestDateTime(?\DateTimeImmutable $testDateTime): self
    {
        $this->updatePropertyValue(
            TestDateTimeFieldInterface::PROP_TEST_DATE_TIME,
            $testDateTime
        );

        return $this;
    }
}
PHP;

    /**
     * @test
     */
    public function itGeneratesTheCorrectContent(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\TestStringFieldTrait';
        $expected     = self::FIELD_TRAIT;
        $actual       = $this->getCreator()
                             ->setMappingHelperCommonType(MappingHelper::TYPE_STRING)
                             ->setUnique(true)
                             ->setNewObjectFqn($newObjectFqn)
                             ->createTargetFileObject()
                             ->getTargetFile()
                             ->getContents();
        self::assertSame(trim($expected), trim($actual));
    }

    private function getCreator(): FieldTraitCreator
    {
        $namespaceHelper = new NamespaceHelper();
        $config          = new Config(ConfigTest::SERVER);

        return new FieldTraitCreator(
            new FileFactory($namespaceHelper, $config),
            $namespaceHelper,
            new Writer(),
            $config,
            new FindReplaceFactory(),
            new CodeHelper($namespaceHelper)
        );
    }

    /**
     * @test
     */
    public function itHandlesDeeplyNestedFieldFqn(): void
    {
        $newObjectFqn =
            'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\Deeply\\Nested\\TestDateTimeFieldTrait';
        $actual       = $this->getCreator()
                             ->setMappingHelperCommonType(MappingHelper::TYPE_DATETIME)
                             ->setUnique(true)
                             ->setNewObjectFqn($newObjectFqn)
                             ->createTargetFileObject()
                             ->getTargetFile()
                             ->getContents();
        self::assertStringContainsString('EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\Deeply\\Nested',
                                         $actual);
    }

    /**
     * @test
     */
    public function itGeneratesTheCorrectContentDatetime(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\TestDateTimeFieldTrait';
        $expected     = self::DATETIME_FIELD_TRAIT;
        $actual       = $this->getCreator()
                             ->setMappingHelperCommonType(MappingHelper::TYPE_DATETIME)
                             ->setUnique(true)
                             ->setNewObjectFqn($newObjectFqn)
                             ->createTargetFileObject()
                             ->getTargetFile()
                             ->getContents();
        self::assertSame(trim($expected), trim($actual));
    }

    /**
     * @test
     */
    public function itRequiresTheSuffix(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\TestArray';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$newObjectFqn must end in FieldTrait');
        $this->getCreator()->createTargetFileObject($newObjectFqn);
    }

    /**
     * @test
     */
    public function itCanCreateABooleanFieldTrait(): void
    {
        $contents = $this->itCanCreateAFieldTrait(MappingHelper::TYPE_BOOLEAN);
        self::assertStringContainsString('function isTestBoolean(): ?bool', $contents);
    }

    /**
     * @test
     *
     * @param string $type
     *
     * @return string
     */
    public function itCanCreateAFieldTrait(string $type = MappingHelper::TYPE_DATETIME): string
    {
        $newObjectFqn =
            'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\Test' . ucfirst($type) . 'FieldTrait';
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
    public function itCanGenerateUniqueFields(): void
    {
        $newObjectFqn = 'EdmondsCommerce\\DoctrineStaticMeta\\Entity\\Fields\\Traits\\TestUniqueStringFieldTrait';
        $contents     = $this->getCreator()
                             ->setMappingHelperCommonType(MappingHelper::TYPE_STRING)
                             ->setUnique(true)
                             ->createTargetFileObject($newObjectFqn)
                             ->getTargetFile()
                             ->getContents();
        self::assertStringContainsString(
            'TestUniqueStringFieldInterface::DEFAULT_TEST_UNIQUE_STRING,
            true',
            $contents
        );
    }

    /**
     * @test
     */
    public function itCreatesStringFieldsWithExtraValidation(): void
    {
        $contents = $this->itCanCreateAFieldTrait(MappingHelper::PHP_TYPE_STRING);
        self::assertStringContainsString('new Length([\'min\' => 0, \'max\' => Database::MAX_VARCHAR_LENGTH])',
                                         $contents);
    }
}

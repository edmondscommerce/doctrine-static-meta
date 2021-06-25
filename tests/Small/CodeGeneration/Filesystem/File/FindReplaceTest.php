<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem\File;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\FindReplace;
use PHPUnit\Framework\TestCase;

//phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\FindReplace
 */
class FindReplaceTest extends TestCase
{

    /* **************************************************************
     * TEST CONTENT STARTS
     * **************************************************************/

    private const  TEST_CONTENTS = <<<'PHP'
<?php declare(strict_types=1);

namespace Test\Code\Generator\Entities;
// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Test\Code\Generator\Entity\Fields\Traits\TextFieldTrait;
use Test\Code\Generator\Entity\Interfaces\CompanyInterface;
use Test\Code\Generator\Entity\Relations\Company\Director\Traits\HasCompanyDirectors\HasCompanyDirectorsOwningManyToMany;
use Test\Code\Generator\Entity\Relations\Some\Client\Traits\HasSomeClient\HasSomeClientOwningOneToOne;
use Test\Code\Generator\Entity\Repositories\CompanyRepository;

// phpcs:enable
class Company implements 
    CompanyInterface
{
	use HasCompanyDirectorsOwningManyToMany;
	
	/**
	 * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @param ClassMetadataBuilder $builder
	 */
	private static function setCustomRepositoryClass(ClassMetadataBuilder $builder) {
		$builder->setCustomRepositoryClass(CompanyRepository::class);
	}

 /**
     * @var ArrayCollection<CompanyInterface>
     */
    private ArrayCollection $companies;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForCompanies(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasCompaniesInterface::PROPERTY_NAME_COMPANIES,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForCompanies(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|CompanyInterface[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /**
     * @param Collection|CompanyInterface[] $companies
     *
     * @return self
     */
    public function setCompanies(
        Collection $companies
    ): HasCompaniesInterface {
        $this->setEntityCollectionAndNotify(
            'companies',
            $companies
        );

        return $this;
    }

    /**
     * @param CompanyInterface|null $company
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCompany(
        ?CompanyInterface $company,
        bool $recip = true
    ): HasCompaniesInterface {
        if ($company === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('companies', $company);
        if ($this instanceof ReciprocatesCompanyInterface && true === $recip) {
            $this->reciprocateRelationOnCompany(
                $company
            );
        }

        return $this;
    }

    /**
     * @param CompanyInterface $company
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompany(
        CompanyInterface $company,
        bool $recip = true
    ): HasCompaniesInterface {
        $this->removeFromEntityCollectionAndNotify('companies', $company);
        if ($this instanceof ReciprocatesCompanyInterface && true === $recip) {
            $this->removeRelationOnCompany(
                $company
            );
        }

        return $this;
    }

    /**
     * Initialise the companies property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initCompanies():static
    {
        $this->companies = new ArrayCollection();

        return $this;
    }
}
PHP;

    /* **************************************************************
     * TEST CONTENT ENDS
     * **************************************************************/

    /**
     * @test
     * @small
     */
    public function itCanDoSimpleStringReplace(): void
    {
        $file   = $this->getFile();
        $object = $this->getFindReplace($file);
        $object->findReplace('use', 'choose');
        self::assertStringNotContainsString('use', $file->getContents());
    }

    private function getFile(): File
    {
        $file = new File();
        $file->setContents(self::TEST_CONTENTS);

        return $file;
    }

    private function getFindReplace(File $file): FindReplace
    {
        return new FindReplace($file);
    }

    /**
     * @test
     * @small
     */
    public function itCanChangeName(): void
    {
        $file   = $this->getFile();
        $object = $this->getFindReplace($file);
        $object->findReplaceName('Company', 'Sheep');
        $contents = $file->getContents();
        self::assertStringNotContainsString('Company', $contents);
        self::assertStringNotContainsString('Companies', $contents);
        self::assertStringNotContainsString('company', $contents);
        self::assertStringNotContainsString('companies', $contents);
        self::assertStringContainsString(
            'class Sheep implements 
    SheepInterface',
            $contents
        );
        self::assertStringContainsString(
            'use HasSheepDirectorsOwningManyToMany;',
            $contents
        );
        self::assertStringContainsString('SheepInterface $sheep', $contents);
        self::assertStringContainsString('private ArrayCollection $sheeps', $contents);
    }

    /**
     * @test
     * @small
     */
    public function itCanEscapeSlashesForRegexAndDoRegexReplace(): void
    {
        $file   = $this->getFile();
        $object = $this->getFindReplace($file);
        $object->findReplaceRegex(
            $object->escapeSlashesForRegex('%Doctrine\\.+?\\Mapping\\' . 'Builder\\ClassMetadataBuilder%'),
            'Foo\\Builder'
        );
        $contents = $file->getContents();
        self::assertStringNotContainsString(ClassMetadataBuilder::class, $contents);
        self::assertStringContainsString('use Foo\\Builder', $contents);
    }
}

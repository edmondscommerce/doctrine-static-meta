<?php declare(strict_types=1);

namespace PHPSTORM_META {

    /**
     * This gives us dynamic type hinting if we use the container as a service locator
     *
     * @see https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
     *
     * This is built using the build.php script
     */
    override(
        \EdmondsCommerce\DoctrineStaticMeta\Container::get(0),
        map([
                '\Ramsey\Uuid\UuidFactory'=>\Ramsey\Uuid\UuidFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\AbstractEntityFactoryCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\AbstractEntityFactoryCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\AbstractEntityRepositoryCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\AbstractEntityRepositoryCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\AbstractEntityTestCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\AbstractEntityTestCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\AbstractTestFakerDataProviderUpdater'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\AbstractTestFakerDataProviderUpdater::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator::class,
                '\Doctrine\Common\Cache\ArrayCache'=>\Doctrine\Common\Cache\ArrayCache::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\BootstrapCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\BootstrapCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Builder\Builder'=>\EdmondsCommerce\DoctrineStaticMeta\Builder\Builder::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver'=>\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\BulkEntitySaver::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CliConfigCommandFactory'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CliConfigCommandFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Config'=>\EdmondsCommerce\DoctrineStaticMeta\Config::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintValidatorCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\PropertyConstraintValidatorCreator::class,
                '\Symfony\Component\Validator\ContainerConstraintValidatorFactory'=>\Symfony\Component\Validator\ContainerConstraintValidatorFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateConstraintAction::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CreateConstraintCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CreateConstraintCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CreateDataTransferObjectsFromEntitiesCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CreateDataTransferObjectsFromEntitiesCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateDtosForAllEntitiesAction::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEntityAction'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action\CreateEntityAction::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Schema\Database'=>\EdmondsCommerce\DoctrineStaticMeta\Schema\Database::class,
                '\Symfony\Component\Validator\Mapping\Cache\DoctrineCache'=>\Symfony\Component\Validator\Mapping\Cache\DoctrineCache::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator'    =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\DataTransferObjects\DtoCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory'                                =>\EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities\EntityCreator'                   =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entities\EntityCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidator'                                =>\EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector'                              =>\EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityDtoFactoryCreator' =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityDtoFactoryCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter'           =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory'=>\EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityFactoryCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Factories\EntityFactoryCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\Entity\Fixtures\EntityFixtureCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Assets\Entity\Fixtures\EntityFixtureCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Interfaces\EntityInterfaceCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Interfaces\EntityInterfaceCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory'=>\EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory::class,
                '\Doctrine\ORM\EntityManagerInterface'=>\Doctrine\ORM\EntityManagerInterface::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\EntityRepositoryCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Repositories\EntityRepositoryCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver'=>\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntitySaverCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Entity\Savers\EntitySaverCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory'=>\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\EntityTestCreator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Tests\Entities\EntityTestCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FileFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider::class,
                '\Symfony\Component\Filesystem\Filesystem'=>\Symfony\Component\Filesystem\Filesystem::class,
                '\Doctrine\Common\Cache\FilesystemCache'=>\Doctrine\Common\Cache\FilesystemCache::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableFromArchetypeCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableFromArchetypeCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateFieldCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateFieldCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverrideCreateCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverrideCreateCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverridesUpdateCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverridesUpdateCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper::class,
                '\Symfony\Component\Validator\Validator\RecursiveValidator'=>\Symfony\Component\Validator\Validator\RecursiveValidator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\ReflectionHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\RemoveUnusedRelationsCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\RemoveUnusedRelationsCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\RepositoryFactory'=>\EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\RepositoryFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Schema\Schema'=>\EdmondsCommerce\DoctrineStaticMeta\Schema\Schema::class,
                '\Doctrine\ORM\Tools\SchemaTool'=>\Doctrine\ORM\Tools\SchemaTool::class,
                '\Doctrine\ORM\Tools\SchemaValidator'=>\Doctrine\ORM\Tools\SchemaValidator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetEmbeddableCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetEmbeddableCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\StandardLibraryTestGenerator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\StandardLibraryTestGenerator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator'=>\EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory'=>\EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper'                                                                  =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover'                                                      =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\UnusedRelationsRemover::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory'                                                        =>\EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidatorFactory'                                               =>\EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityDataValidatorFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer'                                                      =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\EntityIsValidConstraintCreator'          =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\EntityIsValidConstraintCreator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\EntityIsValidConstraintValidatorCreator' =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\Src\Validation\Constraints\EntityIsValidConstraintValidatorCreator::class,
            ]
        )
    );
}
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
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper'                        =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Config'                                           =>\EdmondsCommerce\DoctrineStaticMeta\Config::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Schema\Database'                                  =>\EdmondsCommerce\DoctrineStaticMeta\Schema\Database::class,
                '\Symfony\Component\Validator\Mapping\Cache\DoctrineCache'                             =>\Symfony\Component\Validator\Mapping\Cache\DoctrineCache::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter' => \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\EntityFieldSetter::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator'         =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator::class,
                '\Doctrine\ORM\EntityManager'                                                          =>\Doctrine\ORM\EntityManager::class,
                '\EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory'               =>\EdmondsCommerce\DoctrineStaticMeta\EntityManager\EntityManagerFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidator'                =>\EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory'         =>\EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver'                        =>\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory'                 =>\EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator'    => \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction' =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction::class,
                '\Symfony\Component\Filesystem\Filesystem'                                             =>\Symfony\Component\Filesystem\Filesystem::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand'     =>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateFieldCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateFieldCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateRelationsCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Schema\Schema'=>\EdmondsCommerce\DoctrineStaticMeta\Schema\Schema::class,
                '\EdmondsCommerce\DoctrineStaticMeta\Schema\Schema'=>\EdmondsCommerce\DoctrineStaticMeta\Schema\Schema::class,
                '\Doctrine\ORM\Tools\SchemaTool'=>\Doctrine\ORM\Tools\SchemaTool::class,
                '\Doctrine\ORM\Tools\SchemaValidator'=>\Doctrine\ORM\Tools\SchemaValidator::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper::class,
                '\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper'=>\EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper::class,
            ]
        )
    );
}

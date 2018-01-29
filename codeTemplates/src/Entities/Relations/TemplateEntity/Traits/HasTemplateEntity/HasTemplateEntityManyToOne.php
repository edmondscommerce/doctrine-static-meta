<?php declare(strict_types=1);

namespace TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntity;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entities\Relations\TemplateEntity\Traits\HasTemplateEntityAbstract;

/**
 * Trait HasTemplateEntityManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of TemplateEntity.
 *
 * TemplateEntity has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package TemplateNamespace\Entities\Traits\Relations\TemplateEntity\HasTemplateEntity
 */
trait HasTemplateEntityManyToOne
{
    use HasTemplateEntityAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForTemplateEntity(ClassMetadataBuilder $builder)
    {
//        $builder->addManyToOne(
//            TemplateEntity::getSingular(),
//            TemplateEntity::class,
//            static::getPlural()
//        );
        $meta = $builder->getClassMetadata();
        $meta->mapManyToMany(
            [
                'fieldName'    => TemplateEntity::getSingular(),
                'targetEntity' => TemplateEntity::class,
                'mappedBy'     => static::getPlural(),
                'joinTable'    => [
                    'name'               => static::getPlural() . '_to_' . TemplateEntity::getPlural(),
                    'joinColumns'        => [
                        [
                            'name'                 => TemplateEntity::getSingular() . '_' . TemplateEntity::getIdField(),
                            'referencedColumnName' => TemplateEntity::getIdField(),
                            'nullable'             => true,
                            'unique'               => true,
                            'onDelete'             => null,
                            'columnDefinition'     => null,
                        ]
                    ],
                    'inverseJoinColumns' => [
                        [
                            'name'                 => static::getSingular() . '_' . static::getIdField(),
                            'referencedColumnName' => static::getIdField(),
                            'nullable'             => true,
                            'unique'               => false,
                            'onDelete'             => null,
                            'columnDefinition'     => null,
                        ]
                    ],
                ]
            ]
        );
    }
}

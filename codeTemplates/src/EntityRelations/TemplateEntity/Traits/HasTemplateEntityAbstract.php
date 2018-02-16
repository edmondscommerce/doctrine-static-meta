<?php declare(strict_types=1);

namespace TemplateNamespace\EntityRelations\TemplateEntity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use TemplateNamespace\EntityRelations\TemplateEntity\Interfaces\ReciprocatesTemplateEntity;
use TemplateNamespace\Entities\TemplateEntity;

trait HasTemplateEntityAbstract
{
    /**
     * @var TemplateEntity|null
     */
    private $templateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForTemplateEntity(ClassMetadataBuilder $builder): void;

    /**
     * @return TemplateEntity|null
     */
    public function getTemplateEntity(): ?TemplateEntity
    {
        return $this->templateEntity;
    }

    /**
     * @param TemplateEntity $templateEntity
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(TemplateEntity $templateEntity, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesTemplateEntity && true === $recip) {
            $this->reciprocateRelationOnTemplateEntity($templateEntity);
        }
        $this->templateEntity = $templateEntity;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeTemplateEntity(): UsesPHPMetaDataInterface
    {
        $this->templateEntity = null;

        return $this;
    }
}

<?php declare(strict_types=1);
/**
 * To avoid collisions we use the verbose FQN for everything
 * This inevitably creates excessively long lines
 * So we disable that sniff in this file
 */
//phpcs:disable Generic.Files.LineLength.TooLong
namespace TemplateNamespace\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;

abstract class AbstractEntityTest extends DSM\Testing\AbstractEntityTest
{
}

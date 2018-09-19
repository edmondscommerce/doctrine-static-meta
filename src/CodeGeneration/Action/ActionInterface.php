<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Action;

/**
 * An Action combines multiple creations or modifications
 */
interface ActionInterface
{
    /**
     * This must be the method that actually performs the action
     *
     * All your requirements, configuration and dependencies must be called with individual setters
     */
    public function run(): void;

    public function setProjectRootNamespace(string $projectRootNamespace);

    public function setProjectRootDirectory(string $projectRootDirectory);
}
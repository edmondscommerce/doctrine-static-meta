<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

interface PostProcessorInterface
{
    public function __invoke(string $generated): string;
}

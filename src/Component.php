<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation;

use Keboola\Component\BaseComponent;
use Keboola\ExasolTransformation\Config\Config;
use Keboola\ExasolTransformation\Config\ConfigDefinition;

class Component extends BaseComponent
{
    protected function run(): void
    {
        $connection = ConnectionFactory::createFromConfig($this->getConfig(), $this->getLogger());

        $transformation = new Transformation($connection, $this->getLogger());
        $transformation->processBlocks($this->getConfig()->getBlocks());
    }

    public function getConfig(): Config
    {
        /** @var Config $config */
        $config = parent::getConfig();
        return $config;
    }

    protected function getConfigClass(): string
    {
        return Config::class;
    }

    protected function getConfigDefinitionClass(): string
    {
        return ConfigDefinition::class;
    }
}

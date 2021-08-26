<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\Config;

use InvalidArgumentException;
use Keboola\Component\Config\BaseConfig;
use Keboola\ExasolTransformation\Exception\ApplicationException;

class Config extends BaseConfig
{
    public function getDatabaseHost(): string
    {
        try {
            return $this->getValue(['authorization', 'workspace', 'host']);
        } catch (InvalidArgumentException $exception) {
            throw new ApplicationException('Missing authorization host for workspace');
        }
    }

    public function getDatabasePort(): int
    {
        try {
            return $this->getValue(['authorization', 'workspace', 'port']);
        } catch (InvalidArgumentException $exception) {
            throw new ApplicationException('Missing authorization port for workspace');
        }
    }

    public function getDatabaseUsername(): string
    {
        try {
            return $this->getValue(['authorization', 'workspace', 'user']);
        } catch (InvalidArgumentException $exception) {
            throw new ApplicationException('Missing authorization user for workspace');
        }
    }

    public function getDatabasePassword(): string
    {
        try {
            return $this->getValue(['authorization', 'workspace', 'password']);
        } catch (InvalidArgumentException $exception) {
            throw new ApplicationException('Missing authorization password for workspace');
        }
    }

    public function getDatabaseSchema(): string
    {
        try {
            return $this->getValue(['authorization', 'workspace', 'schema']);
        } catch (InvalidArgumentException $exception) {
            throw new ApplicationException('Missing authorization schema for workspace');
        }
    }

    public function getBlocks(): array
    {
        return array_map(
            fn($v) => new Block($v),
            $this->getValue(['parameters', 'blocks'])
        );
    }
}

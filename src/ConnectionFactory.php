<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Keboola\Component\UserException;
use Keboola\ExasolTransformation\Config\Config;
use Keboola\TableBackendUtils\Connection\Exasol\ExasolConnectionFactory;
use Throwable;

class ConnectionFactory
{
    public static function createFromConfig(Config $config): Connection
    {
        try {
            $connection = ExasolConnectionFactory::getConnection(
                sprintf('%s:%s', $config->getDatabaseHost(), $config->getDatabasePort()),
                $config->getDatabaseUsername(),
                $config->getDatabasePassword(),
                null,
                true
            );

            $connection->executeQuery(sprintf('OPEN SCHEMA "%s"', $config->getDatabaseSchema()));
            $connection->executeQuery(sprintf('ALTER SESSION SET QUERY_TIMEOUT=%d;', $config->getQueryTimeout()));

            return $connection;
        } catch (Throwable $e) {
            throw new UserException('Connection failed: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}

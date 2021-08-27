<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation;

use Doctrine\DBAL\Connection;
use Keboola\ExasolTransformation\Config\Config;
use Keboola\TableBackendUtils\Connection\Exasol\ExasolConnection;
use Keboola\TableBackendUtils\Escaping\Exasol\ExasolQuote;
use Psr\Log\LoggerInterface;

class ConnectionFactory
{
    public static function createFromConfig(Config $config, LoggerInterface $logger): Connection
    {
        $logger->info(sprintf('Connection to "%s".', $config->getDatabaseHost()));
        $connection = ExasolConnection::getConnection(
            sprintf('%s:%s', $config->getDatabaseHost(), $config->getDatabasePort()),
            $config->getDatabaseUsername(),
            $config->getDatabasePassword()
        );

        $connection->connect();

        $logger->info(sprintf('Use schema "%s".', $config->getDatabaseSchema()));
        $connection->executeStatement(sprintf(
            'OPEN SCHEMA %s',
            ExasolQuote::quoteSingleIdentifier($config->getDatabaseSchema())
        ));

        $connection->executeQuery(sprintf(
            'ALTER SESSION SET QUERY_TIMEOUT=%d',
            $config->getQueryTimeout()
        ));

        return $connection;
    }
}

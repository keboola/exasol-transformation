<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\OCI8\Driver;
use Doctrine\DBAL\DriverManager;
use Keboola\Component\UserException;
use Keboola\ExasolTransformation\Config\Config;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use Throwable;

class ConnectionFactory
{
    public static function createFromConfig(Config $config, LoggerInterface $logger): Connection
    {
        try {
            $dbh = new PDO(
                sprintf(
                    'odbc:Driver=exasol;ENCODING=UTF-8;EXAHOST=%s:%s;EXASCHEMA=%s;QUERYTIMEOUT=%d',
                    $config->getDatabaseHost(),
                    $config->getDatabasePort(),
                    $config->getDatabaseSchema(),
                    $config->getQueryTimeout()
                ),
                $config->getDatabaseUsername(),
                $config->getDatabasePassword()
            );
        } catch (PDOException $e) {
            throw new UserException('Connection failed: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }

        try {
            return DriverManager::getConnection([
                'pdo' => $dbh,
                'driverClass' => Driver::class,
            ]);
        } catch (Throwable $e) {
            throw new UserException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}

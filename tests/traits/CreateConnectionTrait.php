<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\TestTraits;

use Doctrine\DBAL\Connection;
use Keboola\TableBackendUtils\Connection\Exasol\ExasolConnection;
use Keboola\TableBackendUtils\Connection\Exasol\ExasolConnectionFactory;

trait CreateConnectionTrait
{
    public function createConnection(): Connection
    {
        $connection = ExasolConnectionFactory::getConnection(
            sprintf('%s:%s', (string) getenv('EXASOL_HOST'), (string) getenv('EXASOL_PORT')),
            (string) getenv('EXASOL_USERNAME'),
            (string) getenv('EXASOL_PASSWORD')
        );

        $connection->executeQuery(
            sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', (string) getenv('EXASOL_SCHEMA'))
        );
        $connection->executeQuery(sprintf('OPEN SCHEMA "%s"', (string) getenv('EXASOL_SCHEMA')));

        return $connection;
    }
}

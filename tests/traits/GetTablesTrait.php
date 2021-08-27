<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\TestTraits;

use Doctrine\DBAL\Connection;
use Keboola\TableBackendUtils\Escaping\Exasol\ExasolQuote;

trait GetTablesTrait
{
    public function getTables(Connection $connection): array
    {
        $sqlTemplate = 'select * from EXA_ALL_TABLES WHERE "TABLE_SCHEMA" = %s;';

        return $connection->executeQuery(
            sprintf($sqlTemplate, ExasolQuote::quote((string) getenv('EXASOL_SCHEMA')))
        )->fetchAllAssociative();
    }
}

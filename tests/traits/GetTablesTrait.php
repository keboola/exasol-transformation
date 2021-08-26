<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\TestTraits;

use Doctrine\DBAL\Connection;

trait GetTablesTrait
{
    public function getTables(Connection $connection): array
    {
        $sqlTemplate = 'select * from EXA_ALL_TABLES WHERE "TABLE_SCHEMA" = \'%s\';';

        return $connection->executeQuery(
            sprintf($sqlTemplate, (string) getenv('EXASOL_SCHEMA'))
        )->fetchAllAssociative();
    }
}

<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\TestTraits;

use Doctrine\DBAL\Connection;

trait GetTableColumnsTrait
{
    public function getTableColumns(Connection $connection, array $table): array
    {
        $sqlTemplate = 'select * from EXA_ALL_COLUMNS WHERE "COLUMN_SCHEMA" = \'%s\' AND "COLUMN_TABLE" = \'%s\';';

        return $connection->executeQuery(
            sprintf(
                $sqlTemplate,
                $table['TABLE_SCHEMA'],
                $table['TABLE_NAME']
            )
        )->fetchAllAssociative();
    }
}

<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation;

use Doctrine\DBAL\Connection;
use Keboola\Component\Manifest\ManifestManager;
use Keboola\Component\Manifest\ManifestManager\Options\OutTableManifestOptions;
use Keboola\Component\UserException;
use Keboola\Datatype\Definition\Exasol as ExasolColumnType;
use Keboola\TableBackendUtils\Column\ColumnInterface;
use Keboola\TableBackendUtils\Table\Exasol\ExasolTableReflection;

class ManifestWriter
{
    private Connection $connection;

    private ManifestManager $manifestManager;

    public function __construct(Connection $connection, ManifestManager $manifestManager)
    {
        $this->connection = $connection;
        $this->manifestManager = $manifestManager;
    }

    public function process(array $outputMappingTables): void
    {
        $schemaName = $this->connection->executeQuery('SELECT CURRENT_SCHEMA;')->fetchFirstColumn()[0];

        $missingTables = [];
        foreach ($outputMappingTables as $outputMappingTable) {
            $tableName = $outputMappingTable['source'];
            if (!$this->processTable($schemaName, $tableName)) {
                $missingTables[] = $tableName;
            }
        }

        // Are there any missing tables?
        if ($missingTables) {
            throw new UserException(sprintf(
                '%s "%s" specified in output were not created by the transformation.',
                count($missingTables) === 1 ? 'Table' : 'Tables',
                implode('", "', $missingTables)
            ));
        }
    }

    private function processTable(string $schemaName, string $tableName): bool
    {
        $tableReflection = new ExasolTableReflection($this->connection, $schemaName, $tableName);
        $columns = $tableReflection->getColumnsDefinitions();
        if ($columns->count() === 0) {
            // Table is missing
            return false;
        }

        $metadata = [];
        /** @var ColumnInterface  $column */
        foreach ($columns as $column) {
            $name = $column->getColumnName();
            $type = $column->getColumnDefinition();
            assert($type instanceof ExasolColumnType);
            $metadata[$name] = $type->toMetadata();
        }

        $data = new OutTableManifestOptions();
        $data->setColumns(array_keys($metadata));
        $data->setColumnMetadata($metadata);
        $this->manifestManager->writeTableManifest($tableName, $data);
        return true;
    }
}

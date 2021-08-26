<?php

declare(strict_types=1);

namespace Keboola\ExasolTransformation\FunctionalTests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Keboola\Csv\CsvWriter;
use Keboola\DatadirTests\AbstractDatadirTestCase;
use Keboola\DatadirTests\DatadirTestSpecificationInterface;
use Keboola\ExasolTransformation\TestTraits\CreateConnectionTrait;
use Keboola\ExasolTransformation\TestTraits\GetTableColumnsTrait;
use Keboola\ExasolTransformation\TestTraits\GetTablesTrait;
use Keboola\TableBackendUtils\Table\Exasol\ExasolTableQueryBuilder;
use Symfony\Component\Filesystem\Filesystem;

class DatadirTest extends AbstractDatadirTestCase
{
    use CreateConnectionTrait;
    use GetTablesTrait;
    use GetTableColumnsTrait;

    private const DB_DUMP_IGNORED_METADATA = [
        'COLUMN_SCHEMA',
        'COLUMN_OWNER',
        'COLUMN_OBJECT_ID',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->dropAllTables();
    }

    /**
     * @dataProvider provideDatadirSpecifications
     */
    public function testDatadir(DatadirTestSpecificationInterface $specification): void
    {
        $tempDatadir = $this->getTempDatadir($specification);

        // Setup initial db state
        $this->dropAllTables();

        // Run script
        $process = $this->runScript($tempDatadir->getTmpFolder());

        // Dump database data & create statement after running the script
        $this->dumpAllTables($tempDatadir->getTmpFolder());

        $this->assertMatchesSpecification($specification, $process, $tempDatadir->getTmpFolder());
    }

    protected function dropAllTables(): void
    {
        $queryBuilder = new ExasolTableQueryBuilder();
        // Drop all tables
        $connection = $this->createConnection();

        foreach ($this->getTables($connection) as $table) {
            $connection->executeQuery(
                $queryBuilder->getDropTableCommand(
                    $table['TABLE_SCHEMA'],
                    $table['TABLE_NAME']
                )
            );
        }
    }


    protected function dumpAllTables(string $tmpDir): void
    {
        // Create output dir
        $dumpDir = $tmpDir . '/out/db-dump';
        $fs = new Filesystem();
        $fs->mkdir($dumpDir, 0777);

        // Create connection and get tables
        $connection = $this->createConnection();
        foreach ($this->getTables($connection) as $table) {
            $this->dumpTable($connection, $table, $dumpDir);
        }
    }

    protected function dumpTable(Connection $connection, array $table, string $dumpDir): void
    {
        // Generate create statement
        $metadata = $this->getTableColumns($connection, $table);

        // Ignore non-static keys
        $metadata = array_map(fn(array $item) => array_filter(
            $item,
            fn(string $key) => !in_array($key, self::DB_DUMP_IGNORED_METADATA, true),
            ARRAY_FILTER_USE_KEY
        ), $metadata);

        // Save create statement
        file_put_contents(
            sprintf('%s/%s.metadata.json', $dumpDir, $table['TABLE_NAME']),
            json_encode($metadata, JSON_PRETTY_PRINT)
        );

        // Dump data
        $this->dumpTableData($connection, $table, $dumpDir);
    }

    protected function dumpTableData(
        Connection $connection,
        array $table,
        string $dumpDir
    ): void {
        $csv = new CsvWriter(sprintf('%s/%s.data.csv', $dumpDir, $table['TABLE_NAME']));

        // Write header
        $columns = array_values(array_map(
            fn(array $col) => $col['COLUMN_NAME'],
            $this->getTableColumns($connection, $table)
        ));
        $csv->writeRow($columns);

        // Write data
        $data = $connection->executeQuery(sprintf(
            'SELECT * FROM %s ORDER BY %s',
            $connection->quoteIdentifier($table['TABLE_NAME']),
            $connection->quoteIdentifier($columns[0])
        ))->fetchAllAssociative();
        foreach ($data as $row) {
            $csv->writeRow($row);
        }
    }
}

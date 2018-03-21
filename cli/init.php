<?php

use DataSource\XmlReader;
use FileSystem\Dearchiver;
use FileSystem\Directory;
use Objects\AddressObjectsImporter;
use Objects\HousesImporter;
use Objects\RoomsImporter;
use Objects\AddressObjectsLevelsImporter;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Application();
$db = $container->getDb();
$dbConfig = $container->getDbConfig();
$dbPath = $container->getDatabaseSourcesDirectory();

set_time_limit(0);

$loader = $container->getInitLoader();
$directory = $loader->load();

// Получаем VersionId поскольку если его не окажется, то сообщение об этом мы получим только в самом конце 15-ти минутного процесса, что не очень приятно.
$versionId = $directory->getVersionId();

if (!$versionId) {
    throw new \Exception("Import fias data not found");
}

DbHelper::runFile($dbConfig, $dbPath . '/01_tables.sql');
DbHelper::runFile($dbConfig, $dbPath . '/02_system_data.sql');

/**
 * Import objects
 * Tables: address_objects
 */

$addressObjectsConfig = $container->getImportConfig('address_objects');
if ($addressObjectsConfig) {
    $addressObjects = new AddressObjectsImporter($db, $addressObjectsConfig['table_name'], $addressObjectsConfig['fields']);
    $addressObjects->setRowsLimit($container->getParserLimit('address_objects'));

    $reader = new XmlReader(
        $directory->getAddressObjectFile(),
        $addressObjectsConfig['node_name'],
        array_keys($addressObjectsConfig['fields']),
        $addressObjectsConfig['filters']
    );

    echo "Insert/Update {$addressObjectsConfig['table_name']}\n";
    $addressObjects->import($reader);
    echo "Finished {$addressObjectsConfig['table_name']}\n";
}

/**
 * Import houses
 * Tables: houses
 */

$housesConfig = $container->getImportConfig('houses');
if ($housesConfig) {
    $houses = new HousesImporter($db, $housesConfig['table_name'], $housesConfig['fields']);
    $houses->setRowsLimit($container->getParserLimit('houses'));

    $reader = new XmlReader(
        $directory->getHousesFile(),
        $housesConfig['node_name'],
        array_keys($housesConfig['fields'])
    );

    echo "Insert/Update {$housesConfig['table_name']}\n";
    $houses->import($reader);
    echo "Finished {$housesConfig['table_name']}\n";
}

/**
 * Import rooms
 * Tables: rooms
 */

$roomsConfig = $container->getImportConfig('rooms');
if ($roomsConfig) {
    $rooms = new RoomsImporter($db, $roomsConfig['table_name'], $roomsConfig['fields']);
    $rooms->setRowsLimit($container->getParserLimit('rooms'));

    $reader = new XmlReader(
        $directory->getRoomsFile(),
        $roomsConfig['node_name'],
        array_keys($roomsConfig['fields'])
    );

    echo "Insert/Update {$roomsConfig['table_name']}\n";
    $rooms->import($reader);
    echo "Finished {$roomsConfig['table_name']}\n";
}

/**
 *
 */

DbHelper::runFile($dbConfig, $dbPath . '/03_indexes.sql');

if($addressObjectsConfig) {
    $addressObjects->modifyDataAfterImport();
}

if ($housesConfig) {
    $houses->modifyDataAfterImport();
}

DbHelper::runFile($dbConfig, $dbPath . '/04_constraints.sql');
DbHelper::runFile($dbConfig, $dbPath . '/05_clean_up.sql');

/**
 *
 */

UpdateLogHelper::addVersionIdToLog($db, $versionId);

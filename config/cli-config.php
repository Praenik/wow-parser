<?php

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Provider\OrmSchemaProvider;
use Doctrine\Migrations\Provider\SchemaProvider;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\ListCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Parser\Factory;

require_once 'init.php';

$entityManager = Factory::GetEntityManager();
$connection = Factory::GetConnection();

$configuration = new Configuration($connection);
$configuration->addMigrationsDirectory('Parser', 'migrations');
$configuration->setAllOrNothing(true);
$configuration->setCheckDatabasePlatform(false);

$storageConfiguration = new TableMetadataStorageConfiguration();
$storageConfiguration->setTableName('doctrine_migration_versions');

$configuration->setMetadataStorageConfiguration($storageConfiguration);

$df = DependencyFactory::fromConnection(
    new ExistingConfiguration($configuration),
    new ExistingConnection($connection)
);
$df->setService(SchemaProvider::class, new OrmSchemaProvider($entityManager));

$helperSet = ConsoleRunner::createHelperSet($entityManager);
$doctrineCli = ConsoleRunner::createApplication($helperSet, [
    new DumpSchemaCommand($df),
    new ExecuteCommand($df),
    new GenerateCommand($df),
    new LatestCommand($df),
    new ListCommand($df),
    new MigrateCommand($df),
    new RollupCommand($df),
    new StatusCommand($df),
    new SyncMetadataCommand($df),
    new VersionCommand($df),
    new DiffCommand($df)
]);

$doctrineCli->run();
#!/usr/bin/env php5
<?php
include_once __DIR__ . '/../bootstrap.php';

use \Dao\Database\Sqlite;
use \Dao\Project\FileSystem as DaoProject;

// Initialize the database
$sqlite = Sqlite::getInstance();
$sqlite->initialize(ROOT_PATH . '/data/database.sqlite', ROOT_PATH . '/install/database/sqlite-schema.sql');

$daoProject = DaoProject::getInstance();
$daoProject->populateDatabase();


#!/usr/bin/env php5
<?php
include_once __DIR__ . '/../bootstrap.php';

use \Dao\Database\Sqlite;
use \Dao\User\FileSystem as DaoUser;
use \Dao\Project\FileSystem as DaoProject;
use \Dao\Issue\FileSystem as DaoIssue;

// Initialize the database
$sqlite = Sqlite::getInstance();
$sqlite->initialize(ROOT_PATH . '/data/database.sqlite', ROOT_PATH . '/install/database/sqlite-schema.sql');

// Populate
$daoUser = DaoUser::getInstance();
$daoUser->populateDatabase();

$daoProject = DaoProject::getInstance();
$daoProject->populateDatabase();

$daoIssue = DaoIssue::getInstance();
$daoIssue->populateDatabase();


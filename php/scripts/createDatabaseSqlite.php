#!/usr/bin/env php5
<?php
include_once __DIR__ . '/../bootstrap.php';

$sqlite = \Dao\Database\Sqlite::getInstance();
$sqlite->initialize(ROOT_PATH . '/data/database.sqlite', ROOT_PATH . '/install/database/sqlite-schema.sql');

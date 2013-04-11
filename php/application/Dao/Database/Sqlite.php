<?php
namespace Dao\Database;

/**
 * Access point of the SQLite database
 */
class Sqlite
{
    use \Neolao\Mixin\Singleton;

    /**
     * Initialization of the database
     *
     * @param   string      $filePath           File path of the database
     * @param   string      $schemaFilePath     File path of the schema
     */
    public function initialize($filePath, $schemaFilePath)
    {
        // Create the directory if necessary
        $directory = pathinfo($filePath, PATHINFO_DIRNAME);
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }

        // If the file does not exist, then create it
        if (!is_file($filePath)) {
            $this->_createDatabase($filePath, $schemaFilePath);
        }
    }

    /**
     * Create the database
     *
     * @param   string      $filePath           File path of the database
     * @param   string      $schemaFilePath     File path of the schema
     */
    protected function _createDatabase($filePath, $schemaFilePath)
    {
        $database   = new \SQLite3($filePath);
        $schema     = file_get_contents($schemaFilePath);

        $database->exec($schema);
    }
}

<?php
namespace Dao\Database;

/**
 * Access point of the SQLite database
 */
class Sqlite
{
    use \Neolao\Mixin\Singleton;

    /**
     * File path of the database
     *
     * @var string
     */
    protected $_filePath;

    /**
     * SQLite3 instance
     *
     * @var \SQLite
     */
    protected $_database;

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

        $this->_filePath = $filePath;
    }

    /**
     * Executes a query
     *
     * @param   string      $query      The SQL query to execute
     * @return  bool                    true if the query succeeded, false otherwise
     */
    public function execute($query)
    {
        $database = $this->_getDatabase();
        return $database->exec($query);
    }

    /**
     * Executes a query and returns a single result
     *
     * @param   string      $query          The SQL query to execute
     * @param   bool        $entireRow      Indicates that the entire row is returned
     */
    public function querySingle($query, $entireRow = false)
    {
        $database = $this->_getDatabase();
        return $database->querySingle($query, $entireRow);
    }

    /**
     * Prepares an SQL statement for execution
     *
     * @param   string          $query      The SQL query to prepare
     * @return  \SQLite3Stmt                The statement
     */
    public function prepare($query)
    {
        $database = $this->_getDatabase();
        return $database->prepare($query);
    }

    /**
     * Get the database instance
     *
     * @return  \SQLite3                        Database instance
     */
    protected function _getDatabase()
    {
        if (!$this->_database) {
            $this->_database = new \SQLite3($this->_filePath);
        }
        return $this->_database;
        
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

        // Import the schema
        $database->exec($schema);

        // Close the database
        $database->close();
    }
}

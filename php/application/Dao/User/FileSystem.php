<?php
namespace Dao\User;

use \Vo\User;
use \Dao\Database\Sqlite;

/**
 * Concrete DAO of users via file system
 */
class FileSystem implements UserInterface
{
    use \Neolao\Mixin\Singleton;

    /**
     * Database instance
     *
     * @var \Dao\Database\Sqlite
     */
    protected $_database;

    /**
     * Constructor
     */
    protected function __construct()
    {
        // Get the database instance
        try {
            $this->_database = Sqlite::getInstance();
        } catch (\Exception $exception) {
        }
    }

    /**
     * Get user by id
     *
     * @param   int         $id         User id
     * @return  \Vo\User                User instance
     */
    public function getById($id)
    {
        // Search in the directory
        $directory = $this->_getDataDirectory();
        $filePath = $directory . '/' . $id . '.json';
        if (is_file($filePath)) {
            $user = $this->_buildUserFromFile($filePath);
            return $user;
        }

        return null;
    }

    /**
     * Get user by email
     *
     * @param   string      $email      User email
     * @return  \Vo\User                User instance
     */
    public function getByEmail($email)
    {
        // @todo Check the cache

        // Search in the database
        try {
            $statement = $this->_database->prepare('SELECT id FROM users WHERE email = :email');
            $statement->bindValue(':email', $email, SQLITE3_TEXT);
            $result = $statement->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);

            if ($row) {
                $userId = (int) $row['id'];
                $user = $this->getById($userId);
                return $user;
            } else {
                return null;
            }
        } catch (\Exception $exception) {
        }

        // The search in the database failed


        // Search in the directory
        $directory  = $this->_getDataDirectory();
        $filePaths  = glob($directory . '/*.json');
        foreach ($filePaths as $filePath) {
            $user = $this->_buildUserFromFile($filePath);
            if ($user->email === $email) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Update a user
     *
     * @param   \Vo\User       $user        User instance
     */
    public function update(User $user)
    {
        // Update the file
        $directory  = $this->_getDataDirectory();
        $filePath   = $directory . '/' . $user->id . '.json';
        $serialized = $user->serializeJson();
        if (!is_dir($directory)) {
            mkdir($directory, 0777 - umask(), true);
        }
        file_put_contents($filePath, $serialized);

        // Update the database
        $this->_databaseUpdate($user);
    }

    /**
     * Delete a user
     *
     * @param   int         $userId         User id
     */
    public function delete($userId)
    {
        // Sanitize the parameter
        $userId = (int) $userId;

        // Delete from the file system
        $directory = $this->_getDataDirectory();
        $filePath = $directory . '/' . $userId . '.json';
        if (is_file($filePath)) {
            unlink($filePath);
        }

        // Delete from the database
        try {
            $this->_database->execute('DELETE FROM users WHERE id = ' . $userId);
        } catch (\Exception $exception) {
        }
    }

    /**
     * Populate the database from the files
     */
    public function populateDatabase()
    {
        $directory  = $this->_getDataDirectory();
        $filePaths  = glob($directory . '/*.json');
        foreach ($filePaths as $filePath) {
            // Get the user instance
            $user = $this->_buildUserFromFile($filePath);

            // Add to the database
            $this->_databaseAdd($user);
        }
    }

    /**
     * Get the directory path of the users datas
     *
     * @return   string          Directory path
     */
    protected function _getDataDirectory()
    {
        return ROOT_PATH . '/data/users';
    }

    /**
     * Build a user instance from a file
     *
     * @param   string      $filePath       File path
     * @return  \Vo\User                    User instance
     */
    protected function _buildUserFromFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new \Exception("The file $filePath is not readable");
        }

        // Get the file content
        $fileContent = file_get_contents($filePath);

        // Create the user instance
        $user = new User();
        $user->unserializeJson($fileContent);

        return $user;
    }

    /**
     * Add to the database
     *
     * @param   \Vo\User    $user           User instance
     */
    public function _databaseAdd(User $user)
    {
        // Prepare the query
        $statement = $this->_database->prepare(
            'INSERT INTO users
                (id, creationDate, modificationDate, email, nickname)
             VALUES 
                (:id, :creationDate, :modificationDate, :email, :nickname)'
        );
        $statement->bindValue(':id',                $user->id,                  SQLITE3_INTEGER);
        $statement->bindValue(':creationDate',      $user->creationDate,        SQLITE3_INTEGER);
        $statement->bindValue(':modificationDate',  $user->modificationDate,    SQLITE3_INTEGER);
        $statement->bindValue(':email',             $user->email,               SQLITE3_TEXT);
        $statement->bindValue(':nickname',          $user->nickname,            SQLITE3_TEXT);

        // Execute the query
        $statement->execute();
    }

    /**
     * Update the database
     *
     * @param   \Vo\User    $user           User instance
     */
    public function _databaseUpdate(User $user)
    {
        // Prepare the query
        $statement = $this->_database->prepare(
            'UPDATE users
             SET
                creationDate        = :creationDate,
                modificationDate    = :modificationDate,
                email               = :email,
                nickname            = :nickname
             WHERE
                id                  = :id'
        );
        $statement->bindValue(':id',                $user->id,                  SQLITE3_INTEGER);
        $statement->bindValue(':creationDate',      $user->creationDate,        SQLITE3_INTEGER);
        $statement->bindValue(':modificationDate',  $user->modificationDate,    SQLITE3_INTEGER);
        $statement->bindValue(':email',             $user->email,               SQLITE3_TEXT);
        $statement->bindValue(':nickname',          $user->nickname,            SQLITE3_TEXT);

        // Execute the query
        $statement->execute();
    }

}

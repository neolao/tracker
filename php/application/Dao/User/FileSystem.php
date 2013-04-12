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

}

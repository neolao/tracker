<?php
/**
 * Package Dao
 */
namespace Dao;

/**
 * DAO of users
 */
class User
{
    use \Neolao\Mixin\Singleton;

    /**
     * Get the directory path of the users datas
     *
     * @return   string          Directory path
     */
    public function getDataDirectory()
    {
        return ROOT_PATH . '/data/users';
    }

    /**
     * Get user by email
     *
     * @param   string      $email      User email
     * @return  \Bo\User                User instance
     */
    public function getByEmail($email)
    {
        // Check the cache

        // Search in the directory
        $directory  = $this->getDataDirectory();
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
     * Build a user instance from a file
     *
     * @param   string      $filePath       File path
     * @return  \Bo\User                    User instance
     */
    protected function _buildUserFromFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new \Exception("The file $filePath is not readable");
        }

        // Get the file content
        $fileContent    = file_get_contents($filePath);

        // Create the user instance
        $user           = new \Bo\User();
        $user->unserializeJson($fileContent);

        return $user;
    }
}

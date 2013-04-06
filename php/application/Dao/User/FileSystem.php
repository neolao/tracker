<?php
namespace Dao\User;

use \Vo\User;

/**
 * Concrete DAO of users via file system
 */
class FileSystem implements UserInterface
{
    use \Neolao\Mixin\Singleton;

    /**
     * Get user by email
     *
     * @param   string      $email      User email
     * @return  \Vo\User                User instance
     */
    public function getByEmail($email)
    {
        // Check the cache

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

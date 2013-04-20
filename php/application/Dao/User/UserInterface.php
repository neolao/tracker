<?php
namespace Dao\User;

use \Vo\User;

/**
 * Interface of a DAO of users
 */
interface UserInterface extends \Neolao\Behavior\Singleton
{
    /**
     * Add a user
     *
     * @param   \Vo\User    $user       User instance
     */
    function add(User $user);

    /**
     * Get user by id
     *
     * @param   int         $id         User id
     * @return  \Vo\User                User instance
     */
    function getById($id);

    /**
     * Get user by email
     *
     * @param   string      $email      User email
     * @return  \Vo\User                User instance
     */
    function getByEmail($email);

    /**
     * Update a user
     *
     * @param   \Vo\User       $user        User instance
     */
    function update(User $user);

    /**
     * Delete a user
     *
     * @param   int         $userId         User id
     */
    function delete($userId);
}

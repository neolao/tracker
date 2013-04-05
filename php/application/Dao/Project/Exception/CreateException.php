<?php
namespace Dao\Project\Exception;

/**
 * An error occurred during the creation
 */
class CreateException extends \OutOfBoundsException
{
    const UNKNOWN                   = 0;
    const CODENAME_ALREADY_EXISTS   = 1;
}

<?php
namespace Dao\Project\Exception;

/**
 * An error occurred during the update
 */
class UpdateException extends \Exception
{
    const UNKNOWN                   = 0;
    const PROJECT_NOT_FOUND         = 1;
    const CODENAME_ALREADY_EXISTS   = 2;
}

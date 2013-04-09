<?php
namespace Bo\Issue\Exception;

/**
 * An error occurred during the update
 */
class UpdateException extends \Exception
{
    const UNKNOWN                   = 0;
    const ISSUE_NOT_FOUND           = 1;
}

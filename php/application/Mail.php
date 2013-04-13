<?php

use \Mail\Provider\DefaultProvider;

/**
 * Mail manager
 */
class Mail
{
    /**
     * Get a mail provider
     *
     * @return  \Mail\Provider\ProviderInterface    Provider instance
     */
    public static function providerFactory()
    {
        return new DefaultProvider();
    }
}

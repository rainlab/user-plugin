<?php namespace RainLab\User\Classes;

use Illuminate\Auth\EloquentUserProvider;

/**
 * UserProvider
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class UserProvider extends EloquentUserProvider
{
    /**
     * newModelQuery adjusts the lookup query to exclude guest accounts
     */
    protected function newModelQuery($model = null)
    {
        return parent::newModelQuery($model)->applyRegistered();
    }
}

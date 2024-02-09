<?php namespace RainLab\User\Models\User;

use Backend\Models\ImportModel;
use ApplicationException;
use Exception;

/**
 * UserExport Model
 */
class UserExport extends ImportModel
{
    /**
     * @var string table used by the model
     */
    protected $table = 'users';

    /**
     * importData
     */
    public function importData($results, $sessionKey = null)
    {
    }
}

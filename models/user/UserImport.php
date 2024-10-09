<?php namespace RainLab\User\Models\User;

use Backend\Models\ImportModel;
use ApplicationException;
use Exception;

/**
 * UserExport Model
 */
class UserImport extends ImportModel
{
    /**
     * @var string table used by the model
     */
    protected $table = 'users';

    /**
     * @var array rules
     */
    public $rules = [];

    /**
     * importData
     */
    public function importData($results, $sessionKey = null)
    {
    }
}

<?php namespace RainLab\User\Models\User;

use Str;
use Backend\Models\ImportModel;
use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use Exception;

/**
 * UserImport Model
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
     * @var array groupCache keyed by lowercase code
     */
    protected $groupCache = [];

    /**
     * importData
     */
    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {
            try {
                if (!$email = array_get($data, 'email')) {
                    $this->logSkipped($row, 'Missing user email');
                    continue;
                }

                $existingUser = User::where('email', $email)->first();

                if ($existingUser && !$this->update_existing) {
                    $this->logSkipped($row, 'User email already exists');
                    continue;
                }

                $user = $existingUser ?: User::make();
                $exists = $user->exists;

                // Set standard attributes
                $except = ['id', 'primary_group', 'groups'];

                foreach (array_except($data, $except) as $attribute => $value) {
                    $user->{$attribute} = $value;
                }

                // Primary group is resolved before saving so the default
                // primary group assigned on create does not override it
                if ($primaryGroupCode = array_get($data, 'primary_group')) {
                    if ($primaryGroupId = $this->resolveGroupId($primaryGroupCode)) {
                        $user->primary_group_id = $primaryGroupId;
                    }
                }

                // A password is required to create a new user. Any supplied
                // password must be plaintext, it is hashed by the Hashable trait
                // on assignment above; a random one is generated otherwise.
                if (!$exists && !$user->password) {
                    $user->generatePassword();
                }

                $user->forceSave();

                // Groups
                if ($groupValue = array_get($data, 'groups')) {
                    $groupIds = $this->resolveGroups($groupValue);
                    if ($groupIds) {
                        $user->groups()->sync($groupIds, false);
                    }
                }

                if ($exists) {
                    $this->logUpdated();
                }
                else {
                    $this->logCreated();
                }
            }
            catch (Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }
        }
    }

    /**
     * resolveGroups parses a pipe-separated list of group codes into group ids.
     */
    protected function resolveGroups($value): array
    {
        $ids = [];

        foreach (explode('|', $value) as $code) {
            if ($id = $this->resolveGroupId($code)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * resolveGroupId finds a group id by code, optionally creating it.
     */
    protected function resolveGroupId($code): ?int
    {
        $code = trim($code);
        if (!strlen($code)) {
            return null;
        }

        $key = mb_strtolower($code);

        if (isset($this->groupCache[$key])) {
            return $this->groupCache[$key];
        }

        $group = UserGroup::where('code', $code)->first();

        if (!$group && $this->auto_create_groups) {
            $group = UserGroup::create([
                'name' => Str::title(str_replace(['-', '_'], ' ', $code)),
                'code' => $code,
            ]);
        }

        if ($group) {
            return $this->groupCache[$key] = $group->id;
        }

        return null;
    }
}

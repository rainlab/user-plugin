<?php namespace RainLab\User\Models\UserLog;

use App;
use View;
use Event;
use Backend;

/**
 * HasModelAttributes
 *
 * @property string $detail
 * @property string $actor_admin_name
 * @property string $actor_user_name
 * @property string $user_backend_linkage
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasModelAttributes
{
    /**
     * getDetailAttribute returns the `detail` attribute
     */
    public function getDetailAttribute()
    {
        $typeName = snake_case(studly_case($this->type));

        $path = plugins_path("rainlab/user/models/userlog/_detail_{$typeName}.php");

        if ($event = Event::fire('rainlab.user.extendLogDetailViewPath', [$this, $this->type], true)) {
            $path = $event;
        }

        return file_exists($path) ? View::file($path, ['record' => $this]) : "{$this->type} event";
    }

    /**
     * getActorNameAttribute returns the `actor_admin_name` attribute
     */
    public function getActorAdminNameAttribute()
    {
        if ($this->created_user_id) {
            return $this->created_user_id === $this->getUserFootprintAuth()->id()
                ? __("You")
                : ($this->created_user?->full_name ?: __("Admin"));
        }

        return __("System");
    }

    /**
     * getActorNameAttribute returns the `actor_user_name` attribute
     */
    public function getActorUserNameAttribute()
    {
        if ($this->user_full_name) {
            return $this->user_full_name;
        }

        if ($this->user_id) {
            return $this->user?->full_name ?: __("User");
        }

        return __("User");
    }

    /**
     * getUserBackendLinkageAttribute returns the `user_backend_linkage` attribute
     */
    public function getUserBackendLinkageAttribute()
    {
        if (App::runningInBackend() && $this->user) {
            return [
                Backend::url("user/users/preview/{$this->user->id}"),
                $this->user->full_name
            ];
        }

        return '';
    }
}

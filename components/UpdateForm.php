<?php namespace RainLab\User\Components;

use Auth;
use Redirect;
use Cms\Classes\ComponentBase;

class UpdateForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Update',
            'description' => 'User management form.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }


    /**
     * Log out the user
     *
     * Usage:
     *   <a data-request="user::onLogout">Sign out</a>
     */
    public function onUpdate()
    {
        $user = $this->user();

        if ($user)
            $user->save(post());

        return Redirect::to('/');
    }


    public function user()
    {
        if (!Auth::check())
            return null;

        return Auth::getUser();
    }

}
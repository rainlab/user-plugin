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
     * Update the user
     */
    public function onUpdate()
    {
        $user = $this->user();

        if ($user)
            $user->save(post());

        if ($redirectUrl = post('redirect'))
            return Redirect::to($redirectUrl);
    }

    public function user()
    {
        return Auth::getUser();
    }

}
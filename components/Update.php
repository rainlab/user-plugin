<?php namespace RainLab\User\Components;

use Auth;
use Redirect;
use Cms\Classes\ComponentBase;

class Update extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Update',
            'description' => 'User management form.'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    /**
     * @var RainLab\User\Models\User The user model
     */
    public $user;

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
        $this->user = $this->page['user'] = Auth::getUser();
    }

    /**
     * Update the user
     */
    public function onUpdate()
    {
        if ($user = $this->user)
            $user->save(post());

        if ($redirectUrl = post('redirect'))
            return Redirect::to($redirectUrl);
    }

}
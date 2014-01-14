<?php namespace Plugins\RainLab\User\Components;

use Modules\Cms\Classes\ComponentBase;

class ResetForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Reset Password',
            'description' => 'Forgotten password form.'
        ];
    }

    public function defineProperties()
    {
        return [
            'code-param' => [
                'title' => 'Code Parameter Name',
                'description' => 'The page URL parameter used for the activation code',
                'type' => 'string',
                'default' => 'code'
            ]
        ];
    }

    public function onRestorePassword()
    {
        $link = $this->controller->currentPageUrl();
    }

    public function onResetPassword()
    {

    }

    public function getCode()
    {
        return $this->controller->param($this->property('code-param'));
    }

}
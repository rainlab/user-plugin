# Upgrade guide

- [Upgrading to 1.1 from 1.0](#upgrade-1.1)

<a name="upgrade-1.1"></a>
## Upgrading To 1.1

### Profile fields

These fields have been removed from the users table: company, phone, street_addr, city, zip, country, state. These columns are harmless to be left in place so to clean up you can use this PHP code or drop the columns manually:

    Schema::table('users', function($table)
    {
        $table->dropColumn('phone');
        $table->dropColumn('company');
        $table->dropColumn('street_addr');
        $table->dropColumn('city');
        $table->dropColumn('zip');
        $table->dropColumn('country_id');
        $table->dropColumn('state_id');
    });

To bring back the profile fields will require a separate plugin, see the *Adding profile fields* section of the documentation.

### Country and State models

Country and State models have been removed and can be replaced by installing the plugin RainLab.Location.


### Adding profile fields

To add extra fields to the User model follow these steps:

1. Create a new plugin and add a requirement to RainLab.User

        public $require = ['RainLab.User'];

1. Create a new migration file in the new plugin **updates/user_add_profile_fields.php**, in the `up()` method contained in the class `UserAddProfileFields` add the following:

        Schema::table('users', function($table)
        {
            $table->string('phone', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('street_addr')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('zip', 20)->nullable();
        });

1. Add the following to the `boot()` method of Plugin.php

        \RainLab\User\Controllers\Users::extendFormFields(function($widget){
            $widget->addTabFields([
                'phone' => ['label' => 'Phone', 'tab' => 'Profile', 'span' => 'left'],
                'company' => ['label' => 'Company', 'tab' => 'Profile', 'span' => 'right'],
                'street_addr' => ['label' => 'Street Address', 'tab' => 'Profile'],
                'city' => ['label' => 'City', 'tab' => 'Profile', 'span' => 'left'],
                'zip' => ['label' => 'Zip', 'tab' => 'Profile', 'span' => 'right']
            ]);
        });

### Adding Country and State

1. Create a new plugin and add a requirement to RainLab.User and RainLab.Location

        public $require = ['RainLab.User', 'RainLab.Location'];

1. Create a new migration file in the new plugin **updates/user_add_location_fields.php**, in the `up()` method contained in the class `UserAddLocationFields` add the following:

        Schema::table('users', function($table)
        {
            $table->integer('country_id')->unsigned()->nullable()->index();
            $table->integer('state_id')->unsigned()->nullable()->index();
        });

1. Add the following to the `boot()` method of Plugin.php

        \RainLab\User\Models\User::extend(function($model) {
            $model->implement[] = 'RainLab.Location.Behaviors.LocationModel';
        });

        \RainLab\User\Controllers\Users::extendFormFields(function($widget){
            $widget->addTabFields([
                'country' => ['label' => 'country', 'type' => 'dropdown', 'tab' => 'Profile', 'span' => 'left'],
                'state' => ['label' => 'state', 'type' => 'dropdown', 'tab' => 'Profile', 'span' => 'right', 'dependsOn' => 'country']
            ]);
        });


# Notes

    1.1.0:
        - !!! Profile fields and Locations have been removed.
    1.1.1:
        - Introduce User Groups.
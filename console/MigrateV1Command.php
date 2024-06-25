<?php namespace RainLab\User\Console;

use Schema;
use Illuminate\Console\Command;

/**
 * MigrateV1Command
 */
class MigrateV1Command extends Command
{
    /**
     * @var string name
     */
    protected $name = 'user:migratev1';

    /**
     * @var string description
     */
    protected $description = 'Drops unused database tables and columns from User plugin v1 and v2';

    /**
     * handle
     */
    public function handle()
    {
        if (!Schema::hasTable('rainlab_user_mail_blockers')) {
            $this->info("Table [rainlab_user_mail_blockers] is not found, nothing to migrate.");
            return;
        }

        Schema::dropIfExists('rainlab_user_mail_blockers');

        $columnsToPrune = [
            'name',
            'surname',
            'reset_password_code',
            'permissions',
            'is_superuser',
            'is_activated',
            'last_login',
        ];

        foreach ($columnsToPrune as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function($table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        $this->info("Successfully cleaned up user table data");
    }

    /**
     * getArguments
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * getOptions
     */
    protected function getOptions()
    {
        return [];
    }
}

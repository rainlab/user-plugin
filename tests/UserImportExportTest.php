<?php

use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use RainLab\User\Models\User\UserImport;
use RainLab\User\Models\User\UserExport;

/**
 * UserImportExportTest covers the user import and export models
 */
class UserImportExportTest extends PluginTestCase
{
    /**
     * makeImport builds an import model with the given options
     */
    protected function makeImport(array $options = []): UserImport
    {
        $import = new UserImport;
        $import->update_existing = $options['update_existing'] ?? false;
        $import->auto_create_groups = $options['auto_create_groups'] ?? false;

        return $import;
    }

    public function testImportCreatesUser()
    {
        $import = $this->makeImport();
        $import->importData([
            ['first_name' => 'Jane', 'email' => 'jane@example.tld', 'username' => 'jane'],
        ]);

        $stats = $import->getResultStats();
        $this->assertEquals(1, $stats->created);
        $this->assertEquals(0, $stats->errorCount);

        $user = User::where('email', 'jane@example.tld')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Jane', $user->first_name);
    }

    public function testImportGeneratesPasswordForNewUser()
    {
        $import = $this->makeImport();
        $import->importData([
            ['first_name' => 'Jane', 'email' => 'jane@example.tld', 'username' => 'jane'],
        ]);

        $user = User::where('email', 'jane@example.tld')->first();
        $this->assertNotEmpty($user->password);
    }

    public function testImportHashesSuppliedPassword()
    {
        $import = $this->makeImport();
        $import->importData([
            ['first_name' => 'Jane', 'email' => 'jane@example.tld', 'username' => 'jane', 'password' => 'MySecret123'],
        ]);

        $user = User::where('email', 'jane@example.tld')->first();

        // The plaintext password must be stored hashed and still verify
        $this->assertNotEquals('MySecret123', $user->password);
        $this->assertTrue(Hash::check('MySecret123', $user->password));
    }

    public function testImportSkipsExistingUserWhenNotUpdating()
    {
        User::create([
            'first_name' => 'Existing',
            'email' => 'dup@example.tld',
            'username' => 'dup',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $import = $this->makeImport(['update_existing' => false]);
        $import->importData([
            ['first_name' => 'Changed', 'email' => 'dup@example.tld', 'username' => 'dup'],
        ]);

        $stats = $import->getResultStats();
        $this->assertEquals(0, $stats->created);
        $this->assertEquals(1, $stats->skippedCount);
        $this->assertEquals(0, $stats->errorCount);

        // The existing record must be untouched
        $this->assertEquals('Existing', User::where('email', 'dup@example.tld')->first()->first_name);
    }

    public function testImportUpdatesExistingUserWhenUpdating()
    {
        User::create([
            'first_name' => 'Existing',
            'email' => 'dup@example.tld',
            'username' => 'dup',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $import = $this->makeImport(['update_existing' => true]);
        $import->importData([
            ['first_name' => 'Changed', 'email' => 'dup@example.tld', 'username' => 'dup'],
        ]);

        $stats = $import->getResultStats();
        $this->assertEquals(1, $stats->updated);
        $this->assertEquals(0, $stats->errorCount);
        $this->assertEquals('Changed', User::where('email', 'dup@example.tld')->first()->first_name);
    }

    public function testImportSkipsRowWithoutEmail()
    {
        $import = $this->makeImport();
        $import->importData([
            ['first_name' => 'NoEmail', 'username' => 'noemail'],
        ]);

        $stats = $import->getResultStats();
        $this->assertEquals(0, $stats->created);
        $this->assertEquals(1, $stats->skippedCount);
    }

    public function testImportResolvesExistingGroups()
    {
        UserGroup::create(['name' => 'Wholesale', 'code' => 'wholesale']);

        $import = $this->makeImport();
        $import->importData([
            ['first_name' => 'Jane', 'email' => 'jane@example.tld', 'username' => 'jane', 'groups' => 'wholesale'],
        ]);

        $user = User::where('email', 'jane@example.tld')->first();
        $this->assertContains('wholesale', $user->groups->pluck('code')->all());
    }

    public function testImportIgnoresUnknownGroupWithoutAutoCreate()
    {
        $import = $this->makeImport(['auto_create_groups' => false]);
        $import->importData([
            ['first_name' => 'Jane', 'email' => 'jane@example.tld', 'username' => 'jane', 'groups' => 'nonexistent'],
        ]);

        $this->assertNull(UserGroup::where('code', 'nonexistent')->first());
        $user = User::where('email', 'jane@example.tld')->first();
        $this->assertNotContains('nonexistent', $user->groups->pluck('code')->all());
    }

    public function testImportAutoCreatesGroup()
    {
        $import = $this->makeImport(['auto_create_groups' => true]);
        $import->importData([
            ['first_name' => 'Jane', 'email' => 'jane@example.tld', 'username' => 'jane', 'groups' => 'vip-tier'],
        ]);

        $group = UserGroup::where('code', 'vip-tier')->first();
        $this->assertNotNull($group);
        $this->assertEquals('Vip Tier', $group->name);
    }

    public function testImportSetsPrimaryGroupOverridingDefault()
    {
        UserGroup::create(['name' => 'Wholesale', 'code' => 'wholesale']);

        $import = $this->makeImport();
        $import->importData([
            ['first_name' => 'Jane', 'email' => 'jane@example.tld', 'username' => 'jane', 'primary_group' => 'wholesale'],
        ]);

        $user = User::where('email', 'jane@example.tld')->first();
        $this->assertEquals('wholesale', $user->primary_group->code);

        // Primary group is mirrored into the secondary groups pivot
        $this->assertContains('wholesale', $user->groups->pluck('code')->all());
    }

    public function testExportReturnsUsersWithEncodedGroups()
    {
        UserGroup::create(['name' => 'Wholesale', 'code' => 'wholesale']);

        $user = User::create([
            'first_name' => 'Jane',
            'email' => 'jane@example.tld',
            'username' => 'jane',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);
        $user->groups()->sync([UserGroup::where('code', 'wholesale')->first()->id], false);

        $export = new UserExport;
        $data = $export->exportData(['email', 'primary_group', 'groups']);

        $row = collect($data)->firstWhere('email', 'jane@example.tld');
        $this->assertNotNull($row);
        $this->assertStringContainsString('wholesale', $row['groups']);
        $this->assertEquals('registered', $row['primary_group']);
    }

    public function testExportNeverIncludesProtectedColumns()
    {
        User::create([
            'first_name' => 'Jane',
            'email' => 'jane@example.tld',
            'username' => 'jane',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $export = new UserExport;
        $data = $export->exportData(['email', 'password', 'remember_token']);
        $row = collect($data)->firstWhere('email', 'jane@example.tld');

        // Sensitive columns are blanked even when requested in the config
        $this->assertSame('', $row['password']);
        $this->assertSame('', $row['remember_token']);
    }

    public function testImportMapsIsActivatedToTimestamp()
    {
        $import = $this->makeImport();
        $import->importData([
            ['first_name' => 'Jane', 'email' => 'jane@example.tld', 'username' => 'jane', 'is_activated' => '1'],
        ]);

        $stats = $import->getResultStats();
        $this->assertEquals(0, $stats->errorCount);
        $this->assertEquals(1, $stats->created);

        // is_activated is an accessor; the import must write activated_at instead
        $user = User::where('email', 'jane@example.tld')->first();
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function testImportExportRoundTrip()
    {
        UserGroup::create(['name' => 'Wholesale', 'code' => 'wholesale']);

        $import = $this->makeImport(['auto_create_groups' => true]);
        $import->importData([
            [
                'first_name' => 'Jane',
                'email' => 'jane@example.tld',
                'username' => 'jane',
                'primary_group' => 'wholesale',
                'groups' => 'retail',
            ],
        ]);

        $export = new UserExport;
        $data = $export->exportData(['email', 'primary_group', 'groups']);
        $row = collect($data)->firstWhere('email', 'jane@example.tld');

        $this->assertEquals('wholesale', $row['primary_group']);
        $this->assertStringContainsString('wholesale', $row['groups']);
        $this->assertStringContainsString('retail', $row['groups']);
    }
}

<?php

use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;

/**
 * UserGroupTest covers the UserGroup model
 */
class UserGroupTest extends PluginTestCase
{
    public function testCreateGroup()
    {
        $group = UserGroup::create([
            'name' => 'Moderators',
            'code' => 'moderators',
            'description' => 'Site moderators',
        ]);

        $this->assertNotNull($group->id);
        $this->assertEquals('Moderators', $group->name);
        $this->assertEquals('moderators', $group->code);
    }

    public function testValidationRequiresName()
    {
        $this->expectException(\October\Rain\Database\ModelException::class);

        UserGroup::create([
            'name' => '',
            'code' => 'invalid',
        ]);
    }

    public function testValidationRequiresCode()
    {
        $this->expectException(\October\Rain\Database\ModelException::class);

        UserGroup::create([
            'name' => 'Valid Name',
            'code' => '',
        ]);
    }

    public function testValidationRequiresUniqueCode()
    {
        UserGroup::create(['name' => 'First', 'code' => 'unique-code']);

        $this->expectException(\October\Rain\Database\ModelException::class);

        UserGroup::create(['name' => 'Second', 'code' => 'unique-code']);
    }

    public function testValidationRejectsInvalidCodeCharacters()
    {
        $this->expectException(\October\Rain\Database\ModelException::class);

        UserGroup::create([
            'name' => 'Invalid',
            'code' => 'has spaces!',
        ]);
    }

    public function testValidationAllowsHyphensAndUnderscoresInCode()
    {
        $group = UserGroup::create([
            'name' => 'Valid Code',
            'code' => 'valid-code_123',
        ]);

        $this->assertNotNull($group->id);
    }

    public function testGetGuestGroup()
    {
        $group = UserGroup::getGuestGroup();

        $this->assertNotNull($group);
        $this->assertEquals(UserGroup::GROUP_GUEST, $group->code);
    }

    public function testGetRegisteredGroup()
    {
        $group = UserGroup::getRegisteredGroup();

        $this->assertNotNull($group);
        $this->assertEquals(UserGroup::GROUP_REGISTERED, $group->code);
    }

    public function testScopeWithoutGuest()
    {
        $groups = UserGroup::withoutGuest()->get();

        foreach ($groups as $group) {
            $this->assertNotEquals(UserGroup::GROUP_GUEST, $group->code);
        }
    }

    public function testDeleteGroupDetachesUsers()
    {
        $group = UserGroup::create([
            'name' => 'Temp Group',
            'code' => 'temp-delete',
        ]);

        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $user->addGroup($group);
        $this->assertTrue($user->inGroup($group, false));

        $groupId = $group->id;
        $group->delete();

        $user->unsetRelation('groups');
        $this->assertFalse($user->inGroup('temp-delete', false));
        $this->assertNull(UserGroup::find($groupId));
    }

    public function testNameMinLength()
    {
        $this->expectException(\October\Rain\Database\ModelException::class);

        UserGroup::create([
            'name' => 'AB',
            'code' => 'short-name',
        ]);
    }
}

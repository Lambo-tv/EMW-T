<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Models\User;

test('destroy returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $notification = App\Models\Notification::factory()->create();
    $authUser = User::factory()->create();

    $response = $this->actingAs($authUser)->delete(route('users.notifications.destroy', [$user, $notification]));

    $response->assertOk();
    $this->assertModelMissing($notification);

    // TODO: perform additional assertions
});

test('destroy aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $notification = App\Models\Notification::factory()->create();
    $authUser = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($authUser)->delete(route('users.notifications.destroy', [$user, $notification]));

    $response->assertForbidden();
});

test('index returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $authUser = User::factory()->create();

    $response = $this->actingAs($authUser)->get(route('users.notifications.index', [$user]));

    $response->assertOk();
    $response->assertViewIs('user.notification.index');

    // TODO: perform additional assertions
});

test('index aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $authUser = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($authUser)->get(route('users.notifications.index', [$user]));

    $response->assertForbidden();
});

test('mass destroy returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $authUser = User::factory()->create();

    $response = $this->actingAs($authUser)->delete(route('users.notifications.mass_destroy', [$user]));

    $response->assertOk();

    // TODO: perform additional assertions
});

test('mass destroy aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $authUser = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($authUser)->delete(route('users.notifications.mass_destroy', [$user]));

    $response->assertForbidden();
});

test('mass update returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $authUser = User::factory()->create();

    $response = $this->actingAs($authUser)->patch(route('users.notifications.mass_update', [$user]), [
        // TODO: send request data
    ]);

    $response->assertOk();

    // TODO: perform additional assertions
});

test('mass update aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $authUser = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($authUser)->patch(route('users.notifications.mass_update', [$user]), [
        // TODO: send request data
    ]);

    $response->assertForbidden();
});

test('show returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $notification = App\Models\Notification::factory()->create();
    $authUser = User::factory()->create();

    $response = $this->actingAs($authUser)->get(route('users.notifications.show', [$user, $notification]));

    $response->assertRedirect(withSuccess(trans('notification.marked-read')));

    // TODO: perform additional assertions
});

test('show aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $notification = App\Models\Notification::factory()->create();
    $authUser = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($authUser)->get(route('users.notifications.show', [$user, $notification]));

    $response->assertForbidden();
});

test('update returns an ok response', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $notification = App\Models\Notification::factory()->create();
    $authUser = User::factory()->create();

    $response = $this->actingAs($authUser)->patch(route('users.notifications.update', [$user, $notification]), [
        // TODO: send request data
    ]);

    $response->assertOk();

    // TODO: perform additional assertions
});

test('update aborts with a 403', function (): void {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $user = User::factory()->create();
    $notification = App\Models\Notification::factory()->create();
    $authUser = User::factory()->create();

    // TODO: perform additional setup to trigger `abort_unless(403)`...

    $response = $this->actingAs($authUser)->patch(route('users.notifications.update', [$user, $notification]), [
        // TODO: send request data
    ]);

    $response->assertForbidden();
});

// test cases...
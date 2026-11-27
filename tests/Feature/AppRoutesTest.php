<?php

use App\Models\Log;
use App\Models\RecyclingCenter;
use App\Models\Review;
use App\Models\User;

function admin(): User
{
    return User::factory()->create(['is_admin' => true]);
}

function centerOwner(): User
{
    return User::factory()->create(['is_center' => true, 'phone_no' => '0123456789']);
}

function contributor(): User
{
    return User::factory()->create();
}

function makeCenter(User $owner, array $attributes = []): RecyclingCenter
{
    $center = new RecyclingCenter;
    $center->forceFill(array_merge([
        'name' => 'Pusat Kitar Semula Shah Alam',
        'phone_no' => '0355550000',
        'address' => 'Seksyen 7, Shah Alam',
        'services' => '{ "services" : ["Paper", "Plastic"] }',
        'is_dropbox' => false,
        'operation_hour' => '9am - 5pm',
        'latitude' => '3.0738',
        'longitude' => '101.5183',
        'owner' => $owner->id,
        'is_verified' => true,
    ], $attributes))->save();

    return $center;
}

function makeReview(RecyclingCenter $center, User $user, int $rating = 4): Review
{
    $review = new Review;
    $review->forceFill([
        'recycling_center' => $center->id,
        'user' => $user->id,
        'rating' => $rating,
        'review' => 'Clean and well run.',
    ])->save();

    return $review;
}

function centerPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Dropbox Seksyen 13',
        'phone_no' => '0355551111',
        'services' => ['Paper', 'Glass'],
        'address' => 'Seksyen 13, Shah Alam',
        'is_dropbox' => 1,
        'latitude' => '3.0800',
        'longitude' => '101.5300',
    ], $overrides);
}

// Public pages

test('public pages render', function (string $uri) {
    $this->get($uri)->assertOk();
})->with(['/', '/map', '/services', '/info', '/privacy', '/contributor/register', '/login', '/register', '/forgot-password']);

test('signed-in pages redirect guests to login', function (string $uri) {
    $this->get($uri)->assertRedirect('/login');
})->with(['/dashboard', '/profile', '/center', '/admin', '/owner', '/review', '/review/create', '/user/1']);

test('health check responds', function () {
    $this->get('/up')->assertOk();
});

// Public JSON API

test('locations API lists only verified centers', function () {
    $owner = centerOwner();
    makeCenter($owner, ['name' => 'Verified Center']);
    makeCenter($owner, ['name' => 'Pending Center', 'is_verified' => false]);

    $this->getJson('/api/locations')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.name', 'Verified Center');
});

test('search API returns top rated verified centers', function () {
    $owner = centerOwner();
    $user = contributor();
    $best = makeCenter($owner, ['name' => 'Best Center']);
    $other = makeCenter($owner, ['name' => 'Other Center']);
    makeCenter($owner, ['name' => 'Pending Center', 'is_verified' => false]);
    makeReview($best, $user, 5);
    makeReview($other, $user, 2);

    $this->getJson('/api/search?r=1')
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonPath('0.name', 'Best Center')
        ->assertJsonPath('0.reviews_avg_rating', '5.0');
});

test('search API finds centers by name', function () {
    $owner = centerOwner();
    makeCenter($owner, ['name' => 'Klang Recycling Hub', 'address' => 'Klang']);
    makeCenter($owner, ['name' => 'Shah Alam Point', 'address' => 'Shah Alam']);

    $this->getJson('/api/search?q=Klang')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.name', 'Klang Recycling Hub');
});

test('reviews API lists a center reviews with their authors', function () {
    $center = makeCenter(centerOwner());
    $user = contributor();
    makeReview($center, $user, 3);

    $this->getJson("/api/{$center->id}/getReviews")
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.rating', 3)
        ->assertJsonPath('0.user_info.name', $user->name)
        ->assertJsonPath('0.center_info.id', $center->id);
});

// Registration and Google sign-in

test('contributors can register', function () {
    $this->post('/contributor/register', [
        'name' => 'Contributor',
        'email' => 'contributor@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticated();
    $user = User::where('email', 'contributor@example.com')->first();
    expect($user->is_center)->toBeFalsy()
        ->and($user->is_admin)->toBeFalsy();
    $this->assertDatabaseHas('logs', ['module' => 'Contributor', 'action' => 'create', 'user' => $user->id]);
});

test('center owners register through the main form', function () {
    $this->post('/register', [
        'name' => 'Owner',
        'email' => 'owner@example.com',
        'phone_no' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect('/dashboard');

    expect(User::where('email', 'owner@example.com')->first()->is_center)->toBeTruthy();
});

test('google sign-in redirects to google', function () {
    config(['services.google' => [
        'client_id' => 'test-client',
        'client_secret' => 'test-secret',
        'redirect' => 'http://localhost/auth/google/callback',
    ]]);

    $response = $this->get('/auth/google');

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toStartWith('https://accounts.google.com/');
});

// Dashboard

test('dashboard renders for every kind of user', function (string $kind) {
    $user = match ($kind) {
        'admin' => admin(),
        'owner' => centerOwner(),
        'contributor' => contributor(),
    };
    $center = makeCenter($kind === 'owner' ? $user : centerOwner());
    makeReview($center, contributor(), 5);
    Log::create(['module' => 'RecyclingCenter', 'model_id' => $center->id, 'action' => 'create', 'user' => $user->id]);

    $this->actingAs($user)->get('/dashboard')->assertOk();
})->with(['admin', 'owner', 'contributor']);

// Recycling centers

test('admin manages recycling centers', function () {
    $admin = admin();
    $owner = centerOwner();
    $center = makeCenter($owner, ['is_verified' => false]);

    $this->actingAs($admin)->get('/center')->assertOk()->assertSee($center->name);
    $this->actingAs($admin)->get('/center/create')->assertOk();
    $this->actingAs($admin)->get("/center/{$center->id}")->assertOk()->assertSee($center->name);
    $this->actingAs($admin)->get("/center/{$center->id}/edit")->assertOk();

    $this->actingAs($admin)
        ->post('/center', centerPayload(['owner' => $owner->id]))
        ->assertSessionHasNoErrors()
        ->assertRedirect('/center');
    $created = RecyclingCenter::where('name', 'Dropbox Seksyen 13')->first();
    expect($created->owner)->toBe($owner->id)
        ->and($created->operation_hour)->toBe('-')
        ->and(json_decode($created->services, true))->toBe(['services' => ['Paper', 'Glass']]);

    $this->actingAs($admin)
        ->put("/center/{$center->id}", centerPayload([
            'name' => 'Renamed Center',
            'is_dropbox' => 0,
            'operation_hour' => '8am - 6pm',
            'owner' => $owner->id,
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect('/center');
    expect($center->fresh()->name)->toBe('Renamed Center')
        ->and($center->fresh()->operation_hour)->toBe('8am - 6pm');

    $this->actingAs($admin)->put("/center/{$center->id}/verify")->assertRedirect('/center');
    expect($center->fresh()->is_verified)->toBeTruthy();

    makeReview($center, contributor());
    $this->actingAs($admin)->delete("/center/{$center->id}")->assertRedirect('/center');
    $this->assertSoftDeleted($center);
    expect(Review::where('recycling_center', $center->id)->count())->toBe(0);

    expect(Log::where('module', 'RecyclingCenter')->pluck('action')->all())
        ->toBe(['create', 'update', 'verify', 'delete']);
});

test('center store validates input', function () {
    $this->actingAs(admin())
        ->post('/center', [])
        ->assertSessionHasErrors(['name', 'phone_no', 'services', 'address', 'is_dropbox', 'owner', 'latitude', 'longitude']);
});

test('owners see only their centers and own what they create', function () {
    $owner = centerOwner();
    $mine = makeCenter($owner, ['name' => 'My Center']);
    makeCenter(centerOwner(), ['name' => 'Someone Else Center']);

    $this->actingAs($owner)->get('/center')
        ->assertOk()
        ->assertSee('My Center')
        ->assertDontSee('Someone Else Center');

    $this->actingAs($owner)
        ->post('/center', centerPayload())
        ->assertSessionHasNoErrors()
        ->assertRedirect('/center');

    expect(RecyclingCenter::where('name', 'Dropbox Seksyen 13')->first()->owner)->toBe($owner->id);
});

// Admins

test('admin manages other admins', function () {
    $admin = admin();
    $other = admin();

    $this->actingAs($admin)->get('/admin')->assertOk()->assertSee($other->email);
    $this->actingAs($admin)->get('/admin/create')->assertOk();
    $this->actingAs($admin)->get("/admin/{$other->id}")->assertOk();
    $this->actingAs($admin)->get("/admin/{$other->id}/edit")->assertOk();

    $this->actingAs($admin)->post('/admin', [
        'name' => 'New Admin',
        'email' => 'new-admin@example.com',
        'phone_no' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasNoErrors()->assertRedirect('/admin');
    expect(User::where('email', 'new-admin@example.com')->first()->is_admin)->toBeTruthy();

    $this->actingAs($admin)->put("/admin/{$other->id}", [
        'name' => 'Renamed Admin',
        'email' => $other->email,
        'phone_no' => '0199999999',
    ])->assertSessionHasNoErrors()->assertRedirect('/admin');
    expect($other->fresh()->name)->toBe('Renamed Admin');

    $this->actingAs($admin)->delete("/admin/{$other->id}")->assertRedirect('/admin');
    $this->assertSoftDeleted($other);
});

// Center owners

test('admin manages center owners', function () {
    $admin = admin();
    $owner = centerOwner();
    $center = makeCenter($owner);
    makeReview($center, contributor());

    $this->actingAs($admin)->get('/owner')->assertOk()->assertSee($owner->email);
    $this->actingAs($admin)->get("/owner/{$owner->id}")->assertOk();
    $this->actingAs($admin)->get("/owner/{$owner->id}/edit")->assertOk();

    $this->actingAs($admin)->put("/owner/{$owner->id}", [
        'name' => 'Renamed Owner',
        'email' => $owner->email,
        'phone_no' => '0188888888',
    ])->assertSessionHasNoErrors()->assertRedirect('/owner');
    expect($owner->fresh()->name)->toBe('Renamed Owner');

    $this->actingAs($admin)->put("/owner/{$owner->id}/verify")->assertRedirect('/owner');
    expect($owner->fresh()->is_verified)->toBeTruthy();

    $this->actingAs($admin)->delete("/owner/{$owner->id}")->assertRedirect('/owner');
    $this->assertSoftDeleted($owner);
    $this->assertSoftDeleted($center);
    expect(Review::where('recycling_center', $center->id)->count())->toBe(0);
});

// Reviews

test('contributors review centers', function () {
    $user = contributor();
    $center = makeCenter(centerOwner());
    $other = makeCenter(centerOwner(), ['name' => 'Fast Review Center']);

    $this->actingAs($user)->get('/review')->assertOk();
    $this->actingAs($user)->get('/review/create')->assertOk()->assertSee($center->name);

    $this->actingAs($user)->post('/review', [
        'recycling_center' => $center->id,
        'rating' => 4,
        'review' => 'Good',
    ])->assertSessionHasNoErrors()->assertRedirect('/review');
    $review = Review::where('recycling_center', $center->id)->first();
    expect($review->user)->toBe($user->id);

    $this->actingAs($user)->get('/review')->assertOk()->assertSee($center->name);
    $this->actingAs($user)->get("/review/{$review->id}")->assertOk();
    $this->actingAs($user)->get("/review/{$review->id}/edit")->assertOk();

    $this->actingAs($user)->put("/review/{$review->id}", ['rating' => 2, 'review' => 'Changed'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/review');
    expect($review->fresh()->rating)->toBe(2);

    $this->actingAs($user)->get("/review/{$other->id}/fast")->assertOk()->assertSee('Fast Review Center');
    $this->actingAs($user)->post("/review/{$other->id}/fast", ['rating' => 5])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/review');
    expect(Review::where('recycling_center', $other->id)->where('user', $user->id)->exists())->toBeTrue();

    // A center the user already reviewed cannot be fast-reviewed again.
    $this->actingAs($user)->get("/review/{$other->id}/fast")->assertRedirect('/review');

    $this->actingAs($user)->delete("/review/{$review->id}")->assertRedirect('/review');
    $this->assertSoftDeleted($review);
});

test('review store validates rating', function () {
    $this->actingAs(contributor())
        ->post('/review', ['recycling_center' => 1, 'rating' => 9])
        ->assertSessionHasErrors('rating');
});

test('admins and owners see the review list', function () {
    $owner = centerOwner();
    $center = makeCenter($owner);
    makeReview($center, contributor());

    $this->actingAs(admin())->get('/review')->assertOk()->assertSee($center->name);
    $this->actingAs($owner)->get('/review')->assertOk()->assertSee($center->name);
});

// Profiles

test('a profile page is for its own user and admins', function () {
    $other = centerOwner();
    $me = contributor();

    $this->actingAs($me)->get("/user/{$me->id}")->assertOk()->assertSee($me->name);
    $this->actingAs($me)->get("/user/{$other->id}")->assertForbidden();
    $this->actingAs(admin())->get("/user/{$other->id}")->assertOk()->assertSee($other->name);
});

// Access and input safety

test('only admins reach the admin and owner pages and verify centers or owners', function () {
    $owner = centerOwner();
    $center = makeCenter($owner, ['is_verified' => false]);

    foreach ([contributor(), $owner] as $user) {
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/create')->assertForbidden();
        $this->actingAs($user)->post('/admin', ['name' => 'X', 'email' => 'x@example.test'])->assertForbidden();
        $this->actingAs($user)->get('/owner')->assertForbidden();
        $this->actingAs($user)->get("/owner/{$owner->id}/edit")->assertForbidden();
        $this->actingAs($user)->delete("/owner/{$owner->id}")->assertForbidden();
        $this->actingAs($user)->put("/owner/{$owner->id}/verify")->assertForbidden();
        $this->actingAs($user)->put("/center/{$center->id}/verify")->assertForbidden();
    }

    expect($center->fresh()->is_verified)->toBeFalsy();
    $this->actingAs(admin())->put("/center/{$center->id}/verify")->assertRedirect(route('center.index'));
    expect($center->fresh()->is_verified)->toBeTruthy();
});

test('contributors cannot open the center pages', function () {
    $center = makeCenter(centerOwner());
    $user = contributor();

    $this->actingAs($user)->get('/center')->assertForbidden();
    $this->actingAs($user)->get('/center/create')->assertForbidden();
    $this->actingAs($user)->post('/center', centerPayload())->assertForbidden();
    $this->actingAs($user)->get("/center/{$center->id}")->assertForbidden();
    $this->actingAs($user)->delete("/center/{$center->id}")->assertForbidden();
    expect(RecyclingCenter::count())->toBe(1);
});

test('a center owner cannot touch another owner\'s center', function () {
    $mine = centerOwner();
    $theirs = makeCenter(centerOwner(), ['name' => 'THEIR CENTER']);

    $this->actingAs($mine)->get("/center/{$theirs->id}")->assertForbidden();
    $this->actingAs($mine)->get("/center/{$theirs->id}/edit")->assertForbidden();
    $this->actingAs($mine)->put("/center/{$theirs->id}", centerPayload(['name' => 'TAKEN']))->assertForbidden();
    $this->actingAs($mine)->delete("/center/{$theirs->id}")->assertForbidden();
    $this->actingAs($mine)->get('/center/99999')->assertNotFound();

    expect($theirs->fresh())->name->toBe('THEIR CENTER')->owner->toBe($theirs->owner)->deleted_at->toBeNull();
});

test('an owner\'s center update is validated', function () {
    $owner = centerOwner();
    $center = makeCenter($owner, ['name' => 'MY CENTER']);

    $this->actingAs($owner)->put("/center/{$center->id}", ['name' => ''])->assertSessionHasErrors(['name', 'services', 'address']);
    expect($center->fresh()->name)->toBe('MY CENTER');

    $this->actingAs($owner)->put("/center/{$center->id}", centerPayload(['name' => 'MY CENTER 2']))->assertRedirect(route('center.index'));
    expect($center->fresh())->name->toBe('MY CENTER 2')->owner->toBe($owner->id);
});

test('services must come from the list and are stored as valid JSON', function () {
    $owner = centerOwner();

    $this->actingAs($owner)->post('/center', centerPayload(['services' => ['Paper', '"] } <script>alert(1)</script>']]))
        ->assertSessionHasErrors('services.1');
    expect(RecyclingCenter::count())->toBe(0);

    $this->actingAs($owner)->post('/center', centerPayload(['services' => ['Paper', 'Used Cooking Oil', 'Paper']]))->assertRedirect();
    expect(json_decode(RecyclingCenter::firstOrFail()->services, true))->toBe(['services' => ['Paper', 'Used Cooking Oil']]);
});

test('reviews can be changed only by their author or an admin', function () {
    $owner = centerOwner();
    $center = makeCenter($owner);
    $author = contributor();
    $review = makeReview($center, $author, 4);
    $stranger = contributor();

    $this->actingAs($stranger)->get("/review/{$review->id}")->assertForbidden();
    $this->actingAs($stranger)->get("/review/{$review->id}/edit")->assertForbidden();
    $this->actingAs($stranger)->put("/review/{$review->id}", ['rating' => 1])->assertForbidden();
    $this->actingAs($stranger)->delete("/review/{$review->id}")->assertForbidden();

    // The reviewed center's owner may read it, not change it
    $this->actingAs($owner)->get("/review/{$review->id}")->assertOk();
    $this->actingAs($owner)->put("/review/{$review->id}", ['rating' => 1])->assertForbidden();
    expect($review->fresh()->rating)->toBe(4);

    $this->actingAs($author)->put("/review/{$review->id}", ['rating' => 5])->assertRedirect(route('review.index'));
    expect($review->fresh()->rating)->toBe(5);
    $this->actingAs(admin())->delete("/review/{$review->id}")->assertRedirect(route('review.index'));
    expect(Review::count())->toBe(0);
});

test('one review per user per center, for centers that exist', function () {
    $center = makeCenter(centerOwner());
    $user = contributor();

    $this->actingAs($user)->post('/review', ['recycling_center' => $center->id, 'rating' => 4])->assertRedirect(route('review.index'));
    $this->actingAs($user)->post('/review', ['recycling_center' => $center->id, 'rating' => 1])->assertSessionHas('danger');
    $this->actingAs($user)->post("/review/{$center->id}/fast", ['rating' => 1])->assertSessionHas('danger');
    $this->actingAs($user)->post('/review', ['recycling_center' => 99999, 'rating' => 3])->assertSessionHasErrors('recycling_center');
    $this->actingAs($user)->post('/review/99999/fast', ['rating' => 3])->assertNotFound();

    expect(Review::count())->toBe(1)->and(Review::first()->rating)->toBe(4);
});

test('the public reviews API shows reviewer names only', function () {
    $center = makeCenter(centerOwner());
    $user = User::factory()->create(['phone_no' => '0198887777', 'google_id' => 'g-123']);
    makeReview($center, $user);

    $json = $this->getJson("/api/{$center->id}/getReviews")->assertOk()->json('0');
    expect($json['user_info'])->toBe(['id' => $user->id, 'name' => $user->name])
        ->and(json_encode($json))->not->toContain($user->email)->not->toContain('0198887777')->not->toContain('g-123');
});

test('search never returns unverified centers, even on an address match', function () {
    $owner = centerOwner();
    makeCenter($owner, ['name' => 'VERIFIED KLANG', 'address' => 'Jalan Klang']);
    makeCenter($owner, ['name' => 'PENDING', 'address' => 'Jalan Klang', 'is_verified' => false]);

    $this->getJson('/api/search?q=Klang')->assertOk()->assertJsonCount(1)->assertJsonPath('0.name', 'VERIFIED KLANG');
});

test('search material filters are bound parameters, not SQL', function () {
    // sqlite has no JSON_CONTAINS; give the test connection one that behaves like MySQL's for this column
    \Illuminate\Support\Facades\DB::connection()->getPdo()->sqliteCreateFunction('JSON_CONTAINS', function ($doc, $value, $path) {
        return in_array(json_decode($value), json_decode($doc, true)['services'] ?? [], true) ? 1 : 0;
    }, 3);
    $owner = centerOwner();
    makeCenter($owner, ['name' => 'PAPER ONLY', 'services' => '{"services":["Paper"]}']);
    makeCenter($owner, ['name' => 'GLASS ONLY', 'services' => '{"services":["Glass"]}']);

    $sql = [];
    \Illuminate\Support\Facades\DB::listen(function ($query) use (&$sql) { $sql[] = $query->sql; });

    $this->getJson('/api/search?' . http_build_query(['f' => ['Glass']]))->assertOk()->assertJsonCount(1)->assertJsonPath('0.name', 'GLASS ONLY');
    $this->getJson('/api/search?' . http_build_query(['f' => ["x\"') OR 1=1 OR ('\""]]))->assertOk()->assertJsonCount(0);
    expect(implode("\n", $sql))->not->toContain('OR 1=1');

    $this->getJson('/api/search?f=Paper')->assertUnprocessable();
});


<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Mercure demo channels
|--------------------------------------------------------------------------
*/

// Private: only the matching user's browser ever gets a grant for this topic.
Broadcast::channel('room.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Presence: the returned array becomes the subscriber's payload, which
// travels through the hub's subscription events to every other subscriber
// of this topic (see docs/concepts/active-subscriptions.md in the mercure
// repo) — that's the "who's here" list.
Broadcast::channel('lobby', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

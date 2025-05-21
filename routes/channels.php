<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('event.{eventId}', function ($user, $eventId) {
    $hasAccess = $user->hasEventAccess($eventId);
    Log::info('Channel authorization', [
        'user_id' => $user->user_id,
        'event_id' => $eventId,
        'has_access' => $hasAccess,
        'role' => $user->role,
    ]);
    return $hasAccess;
});

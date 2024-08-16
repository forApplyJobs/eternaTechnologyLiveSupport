<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('calls.{id}', function ($user, $id) {
    return  $user->id === (int) $id;
});
Broadcast::channel('messages.{id}', function ($user, $id) {
    return  $user->id === (int) $id;
});
Broadcast::channel('offer.{id}', function ($user, $id) {
    return $user->id === (int) $id;
});

Broadcast::channel('answer.{id}', function ($user, $id) {
    return $user->id === (int) $id;
});

Broadcast::channel('iceCandidates.{id}', function ($user, $id) {
    return $user->id === (int) $id;
});

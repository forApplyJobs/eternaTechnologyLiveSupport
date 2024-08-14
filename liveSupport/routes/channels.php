<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.{id}', function ($user, $id) {
    return  $user->id === (int) $id;
});

<?php
namespace App\Libraries;
use Pusher\Pusher;

class PusherFactory
{
    public static function make(): Pusher
    {
        $options = [
            'cluster' => env('PUSHER_CLUSTER'),
            'useTLS'  => filter_var(env('PUSHER_USETLS', true), FILTER_VALIDATE_BOOL),
        ];
        return new Pusher(
            env('PUSHER_KEY'),
            env('PUSHER_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );
    }
}

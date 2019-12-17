<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\InteractsWithQueue;
use JPush\Client;

class PushNotification
{
    protected $client;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DatabaseNotification $notification)
    {
        if (app()->environment('locale')) {
            return;
        }
        $user = $notification->notifiable();
        if (!$user->registration_id) {
            return;
        }

        $this->client->push()
            ->setPlatform('ios')
            ->addRegistrationId($user->registration_id)
            ->iosNotification(strip_tags($notification->data('reply_content')), [
                'sound' => 'sound',
                'badge' => '+1',
            ])
            ->send();
    }
}

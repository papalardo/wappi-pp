<?php

namespace App\Notifications;

use App\NotificationChannels\ChatAPI\ChatAPIChannel;
use App\NotificationChannels\ChatAPI\ChatAPIMessage;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmationOfAttendance extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [ChatAPIChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toChatAPI($notifiable)
    {
        return (new ChatAPIMessage)
                    ->to($notifiable->phone) // your user phone
                    // ->file('/path/to/file','My Photo.jpg')
                    ->content("Olá, {$notifiable->name}. Boa noite. \nSó passei pra lembrar que amanhã amanhã tem expediente. Você poderá ir ? Respoda apenas *[sim]* ou *[não]*");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

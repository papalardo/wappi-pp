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
                    ->content("Olá, {$notifiable->name}. \nSó passei pra lembrar que você tem um compromisso amanhã. \nVocê poderá ir ? \nDigite: *1* caso vá ou *2* caso não poderá ir..");
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

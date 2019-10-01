<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;

use App\Models\User;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $request = Request::all();
        $user = User::where('email', '=', $request['email'])->first();

        $name = [
            'login' => $user->email,
            'login_url' => env('APP_URL'),
            'password' => $user->orig,
            "locale" => App::getLocale()
        ];

        return (new MailMessage)
                ->subject(Lang::getFromJson('Recovery Password Email'))
                ->view('emails.name', ['name' => $name]);
    }
}
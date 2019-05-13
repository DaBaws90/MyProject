<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        VerifyEmail::toMailUsing(function ($notifiable) {
            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify', Carbon::now()->addMinutes(60),
                ['id' => $notifiable->getKey()]
            );
            // $this->verificationUrl($notifiable);

            // Return your mail here...
            return (new MailMessage)
                ->subject('Verify your email address')
                ->greeting("Hi, " . Auth::user()->name)
                ->line('Please, verify your email to get full access to our website')
                ->action('Click here', $verifyUrl)
                ->line("If you did not create an account, no further action is required.")
                ->line("Thank you for choosing us!");
                // ->markdown('emails.verify', ['url' => $verifyUrl]);

        });
    }
}

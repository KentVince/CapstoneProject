<?php

namespace App\Filament\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use GuzzleHttp\RetryMiddleware;

class CustomRegistrationResponse implements RegistrationResponse
{
    protected string $redirectUrl;

    public function __construct(string $redirectUrl)
    {
        $this->redirectUrl= $redirectUrl;   
    }

    public function toResponse($request)
    {
        return redirect($this->redirectUrl);
    }
}
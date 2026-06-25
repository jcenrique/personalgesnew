<?php

namespace App\Filament\Pages\Auth;

use Illuminate\Auth\Events\Registered;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Auth\Pages\Login as BaseAuth;
use Illuminate\Support\Facades\Auth;


class Login extends BaseAuth
{
    /**
     * bw ~ override
     * Override to set the mail key which is passed to the LdapRecord authentication provider
     */

    protected function getCredentialsFromFormData(array $data): array
    {


        return [
            'mail' => $data['email'],
            'password'  => $data['password'],
            'fallback' => [
                'email' => $data['email'],
                'password' => $data['password'],
            ],
        ];
    }
    public function authenticate(): ?LoginResponse
    {


        // Antes del login, comprobamos si existe en la DB
        $existing = \App\Models\User::where('email', $this->form->getState()['email'])->exists();


        $response = parent::authenticate();

        $user = Auth::user();

        if (! $existing) {
            // Es un usuario recién creado por LDAP enviar notificacion de correo para verificar email



            event(new Registered($user));
            $user->assignRole($user->role);
        }

        return $response;
    }


}

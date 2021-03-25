<?php

namespace App\Actions\Fortify;

use App\Models\Role;
use App\Models\User;
use App\Rules\AccountCode;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'code' => ['required', 'string', 'max:5', new AccountCode],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'timezone' => ['required', 'string']
        ])->validate();
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'role_id' => Role::where('code', $input['code'])->first()->id,
            'password' => Hash::make($input['password']),
            'timezone' => $input['timezone']
        ]);
    }
}

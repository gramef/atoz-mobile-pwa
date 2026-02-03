<?php

namespace App\Imports;

use App\User;
use App\Jobs\SendEmail;
use Illuminate\Support\Str;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $email = trim($row['email']);

            if (!$email) {
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if ($this->isInvalidEmail($email)) {
                continue;
            }

            $user = User::where('email', $email)->first();

            if (optional($user)->agent) {
                continue;
            }

            if (!$user) {
                $user = User::create([
                    'first_name' => Str::before(trim($row['name']), ' '),
                    'last_name' => Str::after(trim($row['name']), ' '),
                    'email' => $email,
                    'password' => str_random(60),
                    'enabled' => 1,
                ]);

                $user->assignRole('new-agent');
            }

            SendEmail::dispatch($user, new UserCreatedMail(
                app('auth.password.broker')->createToken($user), 
                $user, 
                'emails.users.created', 
                'Agent Account created'
            ));

            echo $user->email . "\r\n";
        }
    }

    private function isInvalidEmail($email): bool
    {
        return Validator::make(['email' => $email], [
            'email' => 'email'
        ])->fails();
    }
}
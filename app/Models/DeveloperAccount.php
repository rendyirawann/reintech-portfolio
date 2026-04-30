<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class DeveloperAccount extends Model
{
    protected $fillable = ['name', 'email', 'password', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function verifyPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }
}

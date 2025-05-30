<?php

namespace App\Models;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model implements Authenticatable
{
    use HasFactory;

    protected $table = "users";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'username',
        'password',
        'name'
    ];

    // relasi tbl user ke kontak 1:N
    public function contacts(): HasMany {
        return $this->hasMany(Contact::class, "user_id", "id");
    }


    public function getAuthIdentifierName() {
        return 'username';
    }

    public function getAuthIdentifier() {
        return $this->username;
    }

    public function getAuthPasswordName() {
        return 'password';
    }

    public function getAuthPassword() {
        return $this->password;
    }

    public function getRememberToken() {
        return $this->token;
    }

    public function setRememberToken($value) {
        $this->token = $value;
    }

    public function getRememberTokenName() {
        return 'token';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $table = "contacts";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone'
    ];

    // relasi kontak ke user
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    // relasi contact ke address 1:N
    public function addresses(): HasMany {
        return $this->hasMany(Address::class, "contact_id", "id");
    }
}

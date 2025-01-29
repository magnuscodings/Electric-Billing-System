<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'firstName',
        'lastName',
        'middleName',
        'suffix',
        'address',
        // 'stallNumber',
        'email',
        'user_id'
    ];

    public function setMiddleNameAttribute($value)
    {
        $this->attributes['middleName'] = $value ?: '';
    }

    public function setSuffixAttribute($value)
    {
        $this->attributes['suffix'] = $value ?: '';
    }

    protected $appends = ['fullName'];

    public function getFullNameAttribute()
    {
        return collect([
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->suffix
        ])->filter()->join(' ');
    }

    public function meter()
    {
        return $this->hasOne(Meter::class, 'clientId');
    }

    public function billings()
    {
        return $this->hasMany(Billing::class, 'clientId');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'service_area',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket_services()
    {
        return $this->hasMany(Service::class, 'support_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requester_name',
        'client_id',
        'service_area',
        'support_id',
        'status',
        'service',
    ];

    protected $attributes = [
        'status' => false,
        'service' => '',
    ];

    protected $with = ['user'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'client_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'support_id');
    }
}

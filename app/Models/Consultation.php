<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'public_token',
        'guest_name',
        'guest_contact',
        'status',
        'last_message_at',
    ];

    public function messages()
    {
        return $this->hasMany(ConsultationMessage::class);
    }
}

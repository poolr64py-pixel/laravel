<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiTokenUsage extends Model
{
    protected $table = 'ai_token_usage';

    protected $fillable = [
        'user_id',
        'membership_id',
        'tokens_used',
        'action',
        'details',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the membership associated with this usage.
     */
    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}

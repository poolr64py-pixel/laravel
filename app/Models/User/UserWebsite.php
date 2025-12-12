<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWebsite extends Model
{
    use HasFactory;
    
    protected $table = 'user_websites';
    protected $guarded = [];
}

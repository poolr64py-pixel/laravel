<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPropertyContent extends Model
{
    protected $table = 'user_property_contents';
    
    public function property()
    {
        return $this->belongsTo(UserProperty::class, 'property_id');
    }
}

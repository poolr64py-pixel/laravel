<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProperty extends Model
{
    protected $table = 'user_properties';
    
    public function contents()
    {
        return $this->hasMany(UserPropertyContent::class, 'property_id');
    }
    
    public function category()
    {
        return $this->belongsTo(UserPropertyCategory::class, 'category_id');
    }
}

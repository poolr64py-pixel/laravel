<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPropertyCategoryContent extends Model
{
    protected $table = 'user_property_category_contents';
    
    public function category()
    {
        return $this->belongsTo(UserPropertyCategory::class, 'category_id');
    }
}

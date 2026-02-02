<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPropertyCategory extends Model
{
    protected $table = 'user_property_categories';
    
    public function contents()
    {
        return $this->hasMany(UserPropertyCategoryContent::class, 'category_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class CategoryTranslation extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['name', 'lang', 'category_id'];

    public function category(){
    	return $this->belongsTo(Category::class);
    }
}

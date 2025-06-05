<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class PageTranslation extends Model
{
  use PreventDemoModeChanges;

  protected $fillable = ['page_id', 'title', 'content', 'lang'];

  public function page(){
    return $this->belongsTo(Page::class);
  }
}

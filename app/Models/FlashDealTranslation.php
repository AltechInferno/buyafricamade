<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class FlashDealTranslation extends Model
{
  use PreventDemoModeChanges;

  protected $fillable = ['title', 'lang', 'flash_deal_id'];

  public function flash_deal(){
    return $this->belongsTo(FlashDeal::class);
  }

}

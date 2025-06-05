<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class DynamicPopup extends Model
{
    use HasFactory, PreventDemoModeChanges;


    protected $guarded = ['id'];

    protected $fillable = [
        'status', 'title', 'summary','banner','btn_link', 'btn_text', 'btn_text_color', 'btn_background_color', 'show_subscribe_form'
    ];
}

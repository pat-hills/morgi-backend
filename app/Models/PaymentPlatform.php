<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlatform extends Model
{
    use HasFactory;

    protected $table = 'payments_platforms';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'fields'
    ];

    protected $hidden = [
        'description',
        'fields'
    ];

    protected $appends = [
        'form_fields',
        'image'
    ];

    public function getFormFieldsAttribute()
    {
        return json_decode($this->fields);
    }

    public function getImageAttribute()
    {
        return env('APP_URL') . "/payments_platforms_images/{$this->name}.png";
    }

}

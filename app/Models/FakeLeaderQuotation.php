<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakeLeaderQuotation extends Model
{
    use HasFactory;

    protected $table = 'fakes_leaders_quotations';

    protected $fillable = [
        'username',
        'leader_since',
        'dollar_amount_gifted',
        'text'
    ];

    public function getAvatarUrlAttribute()
    {
        return env('APP_URL') . "/storage/leaders_quotations/{$this->username}.png";
    }
}

<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIdentityDocumentPhoto extends Model
{
    use HasFactory;

    //Types => front, back, selfie
    protected $table = 'users_identities_documents_photos';

    protected $fillable = [
        'user_id', 'admin_id', 'path_location', 'status', 'decline_reason', 'type'
    ];

    protected $appends = [
        'url'
    ];

    public function getUrlAttribute()
    {
        return StorageUtils::signUrl($this->path_location);
    }

}

<?php

namespace App\Models;

use App\Utils\StorageUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIdentityDocumentHistory extends Model
{
    use HasFactory;

    protected $table = 'users_identities_documents_histories';

    protected $fillable = [
        'user_id',
        'status',
        'front_path_id',
        'back_path_id',
        'selfie_path_id',
        'admin_id',
        'reason',
        'admin_id'
    ];

    public function getFrontPhotoAttribute()
    {
        return UserIdentityDocumentPhoto::find($this->front_path_id);
    }

    public function getBackPhotoAttribute()
    {
        return UserIdentityDocumentPhoto::find($this->back_path_id);
    }

    public function getSelfiePhotoAttribute()
    {
        return UserIdentityDocumentPhoto::find($this->selfie_path_id);
    }
}

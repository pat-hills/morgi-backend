<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Path extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'key_name',
        'is_subpath',
        'created_by',
        'parent_id',
        'prepend'
    ];

    protected $hidden = [
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'is_subpath' => 'boolean'
    ];

    public function getNameAttribute($value)
    {
        return (isset($this->prepend))
            ? "{$this->prepend} {$value}"
            : $value;
    }

    public function getCreatedByUserAttribute()
    {
        if(!$this->is_subpath){
            return null;
        }

        return User::find($this->created_by);
    }
}

<?php

namespace App\Models;

use App\Providers\FileDbConnection;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'description'
    ];



    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IeltsCollection extends Model
{
    protected $fillable = ['title', 'description'];

    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}

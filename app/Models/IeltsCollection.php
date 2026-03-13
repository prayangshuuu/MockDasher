<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IeltsCollection extends Model
{
    protected $fillable = ['title', 'exam_type', 'year', 'description'];

    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['test_id', 'name'];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}

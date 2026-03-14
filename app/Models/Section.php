<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['test_set_id', 'name'];

    public function testSet()
    {
        return $this->belongsTo(TestSet::class);
    }
}

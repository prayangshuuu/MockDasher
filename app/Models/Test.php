<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = ['book_number', 'year', 'exam_type', 'status'];

    public function testSets()
    {
        return $this->hasMany(TestSet::class);
    }

    public function getTitleAttribute()
    {
        return "IELTS {$this->book_number} {$this->exam_type} {$this->year}";
    }
}

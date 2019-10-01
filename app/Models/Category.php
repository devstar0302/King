<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'score'
    ];

    public function paragraphs(){
        return $this->belongsToMany('App\Models\Paragraph');
    }

    
}

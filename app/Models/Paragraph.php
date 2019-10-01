<?php

namespace App\Models;

use App\Events\ParagraphSaved;
use Illuminate\Database\Eloquent\Model;

class Paragraph extends Model
{
    protected $fillable = [
        'score', 'finding', 'risk', 'repair', 'type', 'name'
    ];

    public function categories(){
        return $this->belongsToMany('App\Models\Category');
    }

    public function frr()
    {
        return $this->hasMany('App\Models\FRR');
    }

    public function getFindingAttribute($value)
    {
    	return explode(';', $value);
    }

    protected $dispatchesEvents = [
        'saved' => ParagraphSaved::class
    ];
}

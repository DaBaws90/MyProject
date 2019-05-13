<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'category_id', 'referenceCode', 'providerRef',
        // , 'precioPCComp', 'precioPCBox',
    ];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    private function getPrizeDifference(){
        return $this->precioPCComp - $this->precioPCBox;
    }

}

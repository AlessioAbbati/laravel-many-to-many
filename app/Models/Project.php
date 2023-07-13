<?php

namespace App\Models;

use App\Models\Type;
use App\Traits\Slugger;
use App\Models\Technology;
// use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Slugger;

    public function getRouteKey()
    {
        return $this->slug;
    }

    public function type() {
        return $this->belongsTo(Type::class);
    }

    public function technologies() {
        return $this->belongsToMany(Technology::class);
    }

    // public static function slugger($string) {


    //     // genera lo slug base

    //     $baseSlug = Str::slug($string);
    //     $i = 1;
    //     $slug = $baseSlug;

    //     // finche lo slug generato è presente nella tabella
    //     while (self::where('slug', $slug)->get()) {
    //         // genera un nuovo slug concatenando il contatore
    //         $slug = $baseSlug . '-' . $i;
    //         // incrementa il contatore
    //         $i++;
    //     }

    //     // ritornare lo slug trovato
    //     return $slug;
    // }
}

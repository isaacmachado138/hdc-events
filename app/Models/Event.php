<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    //cast para indicar que items e um array
    protected $casts = [
        'items' => 'array'
    ];

    protected $dates = ['date'];

    protected $guarded = []; //apenas para mostrar que o post pode ser atualizado sem problemas

    //ligando o usuario aos eventos, para quando pegar um usuario ter os eventos dele
    public function user(){
        return $this->belongsTo('App\Models\User'); //belongsTo -> indica que o mesmo usuario pode ter o mesmo varios eventos
    }
    public function users(){
        return $this->belongsToMany('App\Models\User'); 
    //belongsToMany -> indica que o mesmo evento pode ter o mesmo varios usuarios
    }
}

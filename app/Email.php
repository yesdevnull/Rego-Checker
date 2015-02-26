<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model {
    protected $table = 'emails';

    public function plates() {
        return $this->belongsToMany('App\Plate');
    }
}
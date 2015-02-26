<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Plate extends Model {
    protected $table = 'plates';

    public function emails() {
        return $this->belongsToMany('App\Email');
    }
}
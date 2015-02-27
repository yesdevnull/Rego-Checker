<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Plate
 * @package App
 */
class Plate extends Model {
    /**
     * @var string
     */
    protected $table = 'plates';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function emails() {
        return $this->belongsToMany('App\Email');
    }
}
<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Email
 * @package App
 */
class Email extends Model {
    /**
     * @var string
     */
    protected $table = 'emails';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function plates() {
        return $this->belongsToMany('App\Plate');
    }
}
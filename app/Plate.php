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
     * @var array
     */
    protected $status = [
        0   => 'Unknown',
        1   => 'Searched',
        2   => 'In-Date',
        3   => 'Expired',
        99  => 'Invalid',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function emails() {
        return $this->belongsToMany('App\Email');
    }
}
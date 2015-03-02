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
    public $status_list = [
        0   => 'Unknown',
        1   => 'Searched',
        2   => 'In-Date',
        3   => 'Expired',
        99  => 'Invalid',

        'success' => 2
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function emails() {
        return $this->belongsToMany('App\Email');
    }
}
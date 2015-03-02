<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Plate
 * @package App
 */
class Plate extends Model {
    use SoftDeletes;

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
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    public $status_list = [
        0   => 'Unknown',
        1   => 'Searched',
        2   => 'In-Date',
        3   => 'Expired',
        99  => 'Invalid',

        'success'       => 2,
        'unregistered'  => 3
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function emails() {
        return $this->belongsToMany('App\Email');
    }
}
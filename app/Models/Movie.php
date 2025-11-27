<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Movie
 *
 * @property int $id
 * @property string $title
 * @property int $duration_min
 * @property string|null $rating_code
 *
 * @property Collection|Showtime[] $showtimes
 *
 * @package App\Models
 */
class Movie extends Model
{
    use HasFactory;
	protected $table = 'movie';
	public $timestamps = false;

	protected $casts = [
		'duration_min' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'title',
		'duration_min',
        'genre',
        'poster',
		'rating_code',
		'is_active',
	];

	public function showtimes()
	{
		return $this->hasMany(Showtime::class);
	}
}

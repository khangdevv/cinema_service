<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
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
	protected $table = 'movie';
	public $timestamps = false;

	protected $casts = [
		'duration_min' => 'int'
	];

	protected $fillable = [
		'title',
		'duration_min',
		'rating_code'
	];

	public function showtimes()
	{
		return $this->hasMany(Showtime::class);
	}
}

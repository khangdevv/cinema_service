<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Screen
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $format
 * @property int $row_count
 * @property int $col_count
 * @property bool $is_active
 *
 * @property Collection|Seat[] $seats
 * @property Collection|Showtime[] $showtimes
 *
 * @package App\Models
 */
class Screen extends Model
{
    use HasFactory;
	protected $table = 'screen';
	public $timestamps = false;

	protected $casts = [
		'row_count' => 'int',
		'col_count' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'code',
		'name',
		'format',
		'row_count',
		'col_count',
		'is_active'
	];

	public function seats()
	{
		return $this->hasMany(Seat::class);
	}

	public function showtimes()
	{
		return $this->hasMany(Showtime::class);
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property string $name
 * @property int $price
 * @property bool $is_active
 * 
 * @property Collection|OrderLine[] $order_lines
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'product';
	public $timestamps = false;

	protected $casts = [
		'price' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'name',
		'price',
		'is_active'
	];

	public function order_lines()
	{
		return $this->hasMany(OrderLine::class);
	}
}

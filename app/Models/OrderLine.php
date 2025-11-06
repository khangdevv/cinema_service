<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderLine
 * 
 * @property int $id
 * @property int $order_id
 * @property string $item_type
 * @property int|null $seat_id
 * @property int|null $product_id
 * @property int $qty
 * @property int $unit_price
 * @property int $line_total
 * 
 * @property Order $order
 * @property Product|null $product
 * @property Seat|null $seat
 *
 * @package App\Models
 */
class OrderLine extends Model
{
	protected $table = 'order_line';
	public $timestamps = false;

	protected $casts = [
		'order_id' => 'int',
		'seat_id' => 'int',
		'product_id' => 'int',
		'qty' => 'int',
		'unit_price' => 'int',
		'line_total' => 'int'
	];

	protected $fillable = [
		'order_id',
		'item_type',
		'seat_id',
		'product_id',
		'qty',
		'unit_price',
		'line_total'
	];

	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function seat()
	{
		return $this->belongsTo(Seat::class);
	}
}

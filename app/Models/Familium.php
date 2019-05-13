<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 21 Mar 2019 12:45:26 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Familium
 * 
 * @property int $id_familia
 * @property string $familia
 *
 * @package App\Models
 */
class Familium extends Eloquent
{
	protected $primaryKey = 'id_familia';
	public $timestamps = false;

	protected $fillable = [
		'familia'
	];
}

<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 21 Mar 2019 12:45:26 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class Pccomponente
 * 
 * @property string $codigo
 * @property string $nombre
 * @property string $familia
 * @property float $precio
 * @property string $referencia_fabricante
 * @property string $marca
 * @property string $enlace
 *
 * @package App\Models
 */
class Pccomponente extends Eloquent
{
	protected $primaryKey = 'codigo';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'precio' => 'float'
	];

	protected $fillable = [
		'nombre',
		'familia',
		'precio',
		'referencia_fabricante',
		'marca',
		'enlace'
	];
}

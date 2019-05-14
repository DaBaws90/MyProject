<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 21 Mar 2019 12:45:26 +0000.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;
use PhpParser\Node\Expr\Cast\Double;

/**
 * Class Pcbox
 * 
 * @property string $codigo
 * @property string $nombre
 * @property float $precio
 * @property string $referencia_fabricante
 * @property string $marca
 * @property string $enlace
 *
 * @package App\Models
 */
class Pcbox extends Eloquent
{
	protected $table = 'pcbox';
	protected $primaryKey = 'codigo';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'precio' => 'float'
	];

	protected $fillable = [
		'nombre',
		'precio',
		'referencia_fabricante',
		'marca',
		'enlace',
		'subcategoria'
	];

	// Unused methods beacuse of the data handling structure
	public function getPrizeDifference(Double $prize){
		return round($prize - $this->precio);
	}

	public function getPercentage(Double $prize){
		return round(($this->getPrizeDifference($prize) / $this->precio) * 100);
	}
}

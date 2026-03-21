<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Facture
 *
 * @property int $idf
 * @property int $id
 * @property string $conso
 * @property float $montant_total
 * @property Carbon|null $dateEmission
 * @property string $statut
 *
 * @property-read \App\Models\Abonne $abonne
 *
 * @package App\Models
 */
class Facture extends Model
{
    protected $table      = 'Facture';
    protected $primaryKey = 'idf';
    public $incrementing  = true;
    public $timestamps    = false;

    protected $fillable = [
        'id',
        'conso',
        'montant_total',
        'date_emission',
        'statut',
    ];

    public function abonne()
    {
        return $this->belongsTo(Abonne::class, 'id');
    }
}

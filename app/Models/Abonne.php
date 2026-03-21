<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class Abonne
 *
 * @property int $id
 * @property string|null $nom
 * @property string|null $prenom
 * @property string|null $ville
 * @property string|null $quartier
 * @property string|null $num_compteur
 * @property string|null $type_abonement
 * @property string $mdp
 */
class Abonne extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use HasFactory;

    protected $table = 'abonne';

    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'ville',
        'quartier',
        'num_compteur',
        'type_abonement',
        'mdp',
    ];

    //  mdp caché dans les réponses JSON
    protected $hidden = [
        'mdp',
    ];

    //  Hashage automatique du mdp
    protected $casts = [
        'mdp' => 'hashed',
    ];

    //  Indique à Sanctum d'utiliser  le champ 'mdp' à la place de 'password'
    public function getAuthPassword()
    {
        return $this->mdp;
    }

    public function facture()
    {
        return $this->hasMany(Facture::class, 'id');
    }
}

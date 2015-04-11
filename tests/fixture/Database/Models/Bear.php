<?php namespace FHTeam\LaravelValidator\Tests\Fixture\Database\Models;

use Eloquent;
use FHTeam\LaravelValidator\Validator\Eloquent\EloquentValidatingTrait;

/**
 * Class Bear
 *
 * @property string $name
 * @property string $type
 * @property int    $danger_level
 * @package FHTeam\LaravelRedisCache\Tests\Fixture\Database\Models
 */
class Bear extends Eloquent
{
    use EloquentValidatingTrait;

    // MASS ASSIGNMENT -------------------------------------------------------
    // define which attributes are mass assignable (for security)
    // we only want these 3 attributes able to be filled
    protected $fillable = ['name', 'type', 'danger_level'];

    public $timestamps = false;

    // DEFINE RELATIONSHIPS --------------------------------------------------
    // each bear HAS one fish to eat
    public function fish()
    {
        return $this->hasOne(Fish::class); // this matches the Eloquent model
    }

    // each bear climbs many trees
    public function trees()
    {
        return $this->hasMany(Tree::class);
    }

    // each bear BELONGS to many picnic
    // define our pivot table also
    public function picnics()
    {
        return $this->belongsToMany(Picnic::class);
    }
}

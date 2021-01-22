<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Tag extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    public function childrenTags()
    {
        return $this->hasMany(Tag::class, 'parent_id', 'id')->with('childrenTags');
    }
}

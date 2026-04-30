<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutCard extends Model
{
    protected $fillable = ['icon', 'title', 'content', 'order'];
}

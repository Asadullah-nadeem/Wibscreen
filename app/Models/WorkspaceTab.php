<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceTab extends Model
{
    protected $fillable = ['user_id', 'collection_id', 'title', 'url'];
}

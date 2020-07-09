<?php

namespace EvolutionCMS\EvocmsHistoryDoc\Models;

use Illuminate\Database\Eloquent;

class SiteContentHistory extends Eloquent\Model
{
    protected $table = 'site_content_history';

    protected $casts = [
        'timestamp' => 'int',
        'internalKey' => 'int',
        'action' => 'int'
    ];

    protected $fillable = [
        'resource_id',
        'content',
        'other_data',
        'notice',

    ];
}
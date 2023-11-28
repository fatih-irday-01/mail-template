<?php

namespace Fatihirday\MailTemplate\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected $table = 'mail_templates';

    protected $fillable = [
        'view',
        'subject',
        'body'
    ];

    /**
     * @param Builder $query
     * @param string $view
     * @return MailTemplate|null
     */
    public function scopeGetView(Builder $query, string $view): MailTemplate|null
    {
        return $query->where('view', $view)->first();
    }
}

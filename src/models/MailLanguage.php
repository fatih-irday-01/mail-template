<?php

namespace Fatihirday\MailTemplate\Models;

use Illuminate\Database\Eloquent\Model;

class MailLanguage extends Model
{
    protected $table = 'mail_languages';

    protected $fillable = [
        'language_code',
        'key',
        'value'
    ];
}

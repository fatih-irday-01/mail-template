<?php

namespace Fatihirday\MailTemplate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class MailCacheClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:cache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mail template and language clear';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Cache::tags('mail_template')->flush();
    }
}

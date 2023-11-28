<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Fatihirday\MailTemplate\Enums\LanguageEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_languages', function (Blueprint $table) {
            $table->id();
            $table->enum('language_code', array_column(LanguageEnum::cases(), 'value'))->index();
            $table->string('key');
            $table->text('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_languages');
    }
};

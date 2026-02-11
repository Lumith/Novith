<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


//2021_08_01_000053_add_titles_lang_to_event_types_table
// add en, fr, es, ar titles to event_types table
class AddTitlesLangToEventTypesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('event_types', function (Blueprint $table) {
            $table->text('title_en')->nullable()->after('title');
            $table->text('title_fr')->nullable()->after('title_en');
            $table->text('title_es')->nullable()->after('title_fr');
            $table->text('title_ar')->nullable()->after('title_es');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_types', function (Blueprint $table) {
            $table->dropColumn('title_en');
            $table->dropColumn('title_fr');
            $table->dropColumn('title_es');
            $table->dropColumn('title_ar');
        });
    }
}

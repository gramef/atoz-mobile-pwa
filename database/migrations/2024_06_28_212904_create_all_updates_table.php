<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('allupdates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('user_type', 255);
            $table->unsignedInteger('job_id');
            $table->string('job_type', 255);
            $table->unsignedInteger('agent_id');
            $table->unsignedTinyInteger('new_status');
            $table->char('code', 15);
            $table->date('update_time');
            $table->dateTime('update_date');
            $table->char('comment', 100)->nullable()->default(null);
            $table->enum('deleted', ['Y', 'N'])->default('N');
            $table->timestamps();

            // Indexes
            $table->index('user_id', 'fk_all_updates_user_id');
            $table->index('user_type', 'fk_all_updates_agent_type_roles_name');

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_type')->references('name')->on('roles')->onDelete('cascade');
        });

        // Set the auto-increment starting point
        DB::statement('ALTER TABLE allupdates AUTO_INCREMENT = 35;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allupdates', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['user_type']);
            $table->dropIndex('fk_all_updates_user_id');
            $table->dropIndex('fk_all_updates_agent_type_roles_name');
        });

        Schema::dropIfExists('allupdates');
    }
}

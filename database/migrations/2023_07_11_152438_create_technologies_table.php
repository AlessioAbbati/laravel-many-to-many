<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            // $table->string('slug', 100)->unique();
            $table->string('name', 20);
            
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('technologies');
    }
};

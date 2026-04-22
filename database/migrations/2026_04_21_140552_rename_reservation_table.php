<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::rename('reservation', 'reservations');
    }

    public function down()
    {
        Schema::rename('reservations', 'reservation');
    }
};
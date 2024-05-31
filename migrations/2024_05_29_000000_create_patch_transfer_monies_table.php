<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'patch_transfer_monies',
    function (Blueprint $table) {
        $table->increments('id');

        // created_at & updated_at
        $table->timestamps();
    }
);


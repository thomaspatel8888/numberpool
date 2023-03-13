<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignNumberPivotTable extends Migration
{
    public function up()
    {
        Schema::create('campaign_number', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id', 'campaign_id_fk_8178398')->references('id')->on('campaigns')->onDelete('cascade');
            $table->unsignedBigInteger('number_id');
            $table->foreign('number_id', 'number_id_fk_8178398')->references('id')->on('numbers')->onDelete('cascade');
        });
    }
}

<?php

use App\Model\Inventory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $unitType = [
            Inventory::UNIT_TYPE_VEHICLE,
            Inventory::UNIT_TYPE_STARSHIP
        ];
        $criteria = [
            Inventory::CRITERIA_PM,
            Inventory::CRITERIA_EQ,
            Inventory::CRITERIA_GT,
            Inventory::CRITERIA_LT,
            Inventory::CRITERIA_GTE,
            Inventory::CRITERIA_LTE,
        ];
        Schema::create('inventory', function (Blueprint $table) use ($unitType, $criteria) {
            $table->bigIncrements('id');
            $table->enum('unit_type', $unitType)->nullable(false);
            $table->enum('criteria', $criteria)->nullable(true);
            $table->string('tag')->nullable(true);
            $table->json('payload')->nullable(true);
            $table->integer('count')->default(0)->nullable(false)->unsigned();
            $table->timestamps();
            $table->unique(['unit_type', 'tag', 'criteria'], 'unique_unit_type_field_tag_criteria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}

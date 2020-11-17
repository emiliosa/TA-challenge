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
        $criteria = [
            Inventory::CRITERIA_PM,
            Inventory::CRITERIA_EQ,
            Inventory::CRITERIA_GT,
            Inventory::CRITERIA_LT,
            Inventory::CRITERIA_GTE,
            Inventory::CRITERIA_LTE,
        ];
        Schema::create('inventory', function (Blueprint $table) use ($criteria) {
            $table->bigIncrements('id');
            $table->enum('unit_type', ['vehicle', 'starship'])->nullable(false);
            $table->enum('criteria', $criteria)->nullable(false);
            $table->string('tag')->nullable(false);
            $table->json('payload')->nullable(false);
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

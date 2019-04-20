<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string("category_code")->comment('资产类别编号');
            $table->string('name')->default('')->comment('资产类别名称');
            $table->integer("pid")->default(0)->comment('父级');
            $table->string("path")->default("")->comment('父级id层级路径');
            $table->boolean("status")->default(1)->comment('0不可用，1可用');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['name', 'pid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}

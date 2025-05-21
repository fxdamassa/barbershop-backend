<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServicoIdToAgendarCortesTable extends Migration
{
    public function up(): void
    {
        Schema::table('agendar_cortes', function (Blueprint $table) {
            $table->unsignedBigInteger('servico_id')->after('usuario_id');

            $table->foreign('servico_id')
                ->references('id')
                ->on('servicos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('agendar_cortes', function (Blueprint $table) {
            $table->dropForeign(['servico_id']);
            $table->dropColumn('servico_id');
        });
    }
}

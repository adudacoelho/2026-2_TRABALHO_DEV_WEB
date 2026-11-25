<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFranquiaForeignKeyToLojas extends Migration
{
    public function up()
    {
        $this->forge->addForeignKey('franquia_id', 'franquias', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->processIndexes('lojas');
    }

    public function down()
    {
        $this->forge->dropForeignKey('lojas', 'lojas_franquia_id_foreign');
    }
}

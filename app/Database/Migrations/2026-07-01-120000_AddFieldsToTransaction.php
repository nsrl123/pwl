<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToTransaction extends Migration
{
    public function up()
    {
        $fields = [
            'ppn' => [
                'type' => 'DOUBLE',
                'null' => TRUE,
            ],
            'biaya_admin' => [
                'type' => 'DOUBLE',
                'null' => TRUE,
            ],
            'voucher_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE,
            ],
            'diskon_voucher' => [
                'type' => 'DOUBLE',
                'null' => TRUE,
            ],
        ];

        $this->forge->addColumn('transaction', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transaction', 'ppn');
        $this->forge->dropColumn('transaction', 'biaya_admin');
        $this->forge->dropColumn('transaction', 'voucher_code');
        $this->forge->dropColumn('transaction', 'diskon_voucher');
    }
}

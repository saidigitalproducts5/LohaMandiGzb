<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVehiclesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'transporter_id'=> ['type' => 'BIGINT', 'unsigned' => true],
            'vehicle_number'=> ['type' => 'VARCHAR', 'constraint' => 32],
            'driver_name'   => ['type' => 'VARCHAR', 'constraint' => 120],
            'driver_phone'  => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'vehicle_type'  => ['type' => 'VARCHAR', 'constraint' => 64],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'default' => new \CodeIgniter\I18n\Time()],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('transporter_id');
        $this->forge->createTable('vehicles', true);

        $db = \Config\Database::connect();
        if (!$db->fieldExists('driver_phone', 'vehicles')) {
            $db->query('ALTER TABLE vehicles ADD COLUMN driver_phone VARCHAR(15) NULL AFTER driver_name');
        }

        // Link booking to a specific vehicle (nullable) + track creator.
        if (!$db->fieldExists('vehicle_id', 'transport_bookings')) {
            $db->query('ALTER TABLE transport_bookings ADD COLUMN vehicle_id BIGINT UNSIGNED NULL AFTER vehicle_type, ADD KEY idx_tb_vehicle (vehicle_id)');
        }
        if (!$db->fieldExists('created_by', 'transport_bookings')) {
            $db->query('ALTER TABLE transport_bookings ADD COLUMN created_by BIGINT UNSIGNED NULL AFTER order_id, ADD KEY idx_tb_created_by (created_by)');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('vehicle_id', 'transport_bookings')) {
            $db->query('ALTER TABLE transport_bookings DROP COLUMN vehicle_id');
        }
        $this->forge->dropTable('vehicles', true);
    }
}

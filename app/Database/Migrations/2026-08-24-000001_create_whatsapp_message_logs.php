<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWhatsAppMessageLogs extends Migration
{
    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS whatsapp_message_logs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NULL,
            user_type VARCHAR(50) NULL,
            phone VARCHAR(20) NOT NULL,
            normalized_phone VARCHAR(20) NOT NULL,
            template_name VARCHAR(100) NOT NULL,
            trigger_source VARCHAR(100) NULL,
            send_status ENUM('success','failed') DEFAULT 'failed',
            http_status SMALLINT UNSIGNED NULL,
            error_code INT NULL,
            error_message TEXT NULL,
            api_response TEXT NULL,
            composed_message TEXT NULL,
            created_at DATETIME NULL,
            KEY idx_phone (phone),
            KEY idx_user_id (user_id),
            KEY idx_trigger_source (trigger_source),
            KEY idx_send_status (send_status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        db_connect()->query($sql);
    }

    public function down()
    {
        db_connect()->query("DROP TABLE IF EXISTS whatsapp_message_logs");
    }
}
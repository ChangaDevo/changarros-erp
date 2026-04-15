<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite no soporta modificar columnas con CHECK constraints directamente.
        // Recreamos la tabla con la nueva estructura.
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('CREATE TABLE users_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            email_verified_at TEXT,
            role TEXT NOT NULL DEFAULT "admin" CHECK(role IN ("admin", "client", "superadmin")),
            cliente_id INTEGER,
            activo INTEGER NOT NULL DEFAULT 1,
            password TEXT NOT NULL,
            remember_token TEXT,
            created_at TEXT,
            updated_at TEXT
        )');

        DB::statement('INSERT INTO users_new (id, name, email, email_verified_at, role, cliente_id, activo, password, remember_token, created_at, updated_at)
            SELECT id, name, email, email_verified_at, role, cliente_id, 1, password, remember_token, created_at, updated_at FROM users');

        DB::statement('DROP TABLE users');
        DB::statement('ALTER TABLE users_new RENAME TO users');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('CREATE TABLE users_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            email_verified_at TEXT,
            role TEXT NOT NULL DEFAULT "admin" CHECK(role IN ("admin", "client")),
            cliente_id INTEGER,
            password TEXT NOT NULL,
            remember_token TEXT,
            created_at TEXT,
            updated_at TEXT
        )');

        DB::statement('INSERT INTO users_new (id, name, email, email_verified_at, role, cliente_id, password, remember_token, created_at, updated_at)
            SELECT id, name, email, email_verified_at, CASE WHEN role = "superadmin" THEN "admin" ELSE role END, cliente_id, password, remember_token, created_at, updated_at FROM users');

        DB::statement('DROP TABLE users');
        DB::statement('ALTER TABLE users_new RENAME TO users');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};

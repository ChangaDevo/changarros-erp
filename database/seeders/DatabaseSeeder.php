<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\Entrega;
use App\Models\Pago;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create superadmin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@espiral.com',
            'password' => Hash::make('superadmin123'),
            'role' => 'superadmin',
            'activo' => true,
        ]);

        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@changarrito.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'activo' => true,
        ]);

        // Create demo client
        $cliente = Cliente::create([
            'nombre_empresa' => 'Empresa Demo S.A.',
            'nombre_contacto' => 'Juan Pérez',
            'email' => 'cliente@demo.com',
            'telefono' => '555-1234',
            'rfc' => 'DEMO123456ABC',
            'notas' => 'Cliente de demostración',
            'activo' => true,
        ]);

        // Create client user
        User::create([
            'name' => 'Juan Pérez',
            'email' => 'cliente@demo.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'cliente_id' => $cliente->id,
            'activo' => true,
        ]);

        // Create demo project
        $proyecto = Proyecto::create([
            'cliente_id' => $cliente->id,
            'nombre' => 'Identidad Visual Corporativa',
            'descripcion' => 'Desarrollo de identidad visual completa incluyendo logo, paleta de colores y manual de marca.',
            'estado' => 'en_desarrollo',
            'monto_total' => 25000,
            'fecha_inicio' => now()->subDays(30),
            'fecha_entrega_estimada' => now()->addDays(30),
            'creado_por' => 1,
        ]);

        // Create demo delivery
        Entrega::create([
            'proyecto_id' => $proyecto->id,
            'titulo' => 'Propuesta de Logo - Primera versión',
            'descripcion' => 'Tres propuestas de logo para revisión del cliente.',
            'tipo' => 'diseno_inicial',
            'estado' => 'enviado',
            'fecha_entrega' => now(),
            'orden' => 1,
        ]);

        // Create demo payment
        Pago::create([
            'proyecto_id' => $proyecto->id,
            'concepto' => 'Anticipo 50%',
            'monto' => 12500,
            'estado' => 'pendiente',
            'fecha_vencimiento' => now()->addDays(7),
        ]);
    }
}

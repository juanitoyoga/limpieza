<?php

namespace Database\Seeders;

use App\Models\Proveedor; // O ajusta según la ubicación de tu modelo Proveedor
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            // ==================== SUMINISTROS Y TECNOLOGÍA ====================
            [
                'ruc'             => '1790040275001',
                'razon_social'    => 'PA-CO COMERCIAL E INDUSTRIAL S.A.',
                'nombre_comercial' => 'PA-CO Papelería',
                'actividad'       => 'Venta de papelería, útiles de oficina y equipos de computación',
                'email'           => 'ventas@pa-co.com.ec',
                'telefono'        => '022641548',
                'direccion'       => 'Av. 10 de Agosto N24-118 y Colón, Quito, Pichincha',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],
            [
                'ruc'             => '1792499801001',
                'razon_social'    => 'PROVEEDORES DE UPS PROVUPS CIA. LTDA.',
                'nombre_comercial' => 'OFICENT / PROVUPS',
                'actividad'       => 'Mantenimiento preventivo y correctivo de equipos tecnológicos y UPS',
                'email'           => 'admin@oficent.com.ec',
                'telefono'        => '022501264',
                'direccion'       => 'Calle Manuel Iturrey E10-08 y Coruña, Parroquia González Suárez, Quito',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],
            [
                'ruc'             => '1790011674001',
                'razon_social'    => 'IBM DEL ECUADOR C.A.',
                'nombre_comercial' => 'IBM Ecuador',
                'actividad'       => 'Servicios informáticos, desarrollo de software y servidores',
                'email'           => 'contacto@ec.ibm.com',
                'telefono'        => '022970100',
                'direccion'       => 'Av. Amazonas N36-69 y Corea, Edificio IBM, Quito',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],

            // ==================== FERRETERÍA, CONSTRUCCIÓN Y OBRAS ====================
            [
                'ruc'             => '1790008959001',
                'razon_social'    => 'ACERO COMERCIAL ECUATORIANO S.A.',
                'nombre_comercial' => 'Acero Comercial',
                'actividad'       => 'Comercialización de tubos, perfiles, acero y herramientas de construcción',
                'email'           => 'info@acerocomercial.com',
                'telefono'        => '022564200',
                'direccion'       => 'Av. De la Prensa N45-12 y Zamora, Sector La Concepción, Quito',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],
            [
                'ruc'             => '1791304667001',
                'razon_social'    => 'ACEROS BOEHLER DEL ECUADOR S.A. BOEHLER',
                'nombre_comercial' => 'Aceros Boehler',
                'actividad'       => 'Venta de aceros especiales y soldaduras industriales',
                'email'           => 'ventas@boehler.ec',
                'telefono'        => '022483120',
                'direccion'       => 'Panamericana Norte Km 7.5 y Av. Geovanny Calles, Carcelén, Quito',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],
            [
                'ruc'             => '1791807634001',
                'razon_social'    => 'PROVEFABRICA CIA. LTDA.',
                'nombre_comercial' => 'Provefábrica',
                'actividad'       => 'Insumos de ferretería pesada, maquinaria y accesorios de protección personal',
                'email'           => 'contacto@provefabrica.com.ec',
                'telefono'        => '022378900',
                'direccion'       => 'Av. Ilaló N2-15 y Geovanny Farina, Sangolquí, Pichincha',
                'canton'          => 'Rumiñahui',
                'is_active'        => true,
            ],

            // ==================== ALIMENTACIÓN Y CONSUMO ====================
            [
                'ruc'             => '1790016919001',
                'razon_social'    => 'CORPORACION FAVORITA C.A.',
                'nombre_comercial' => 'Supermaxi / Akí',
                'actividad'       => 'Venta al por mayor y menor de víveres, productos de limpieza y hogar',
                'email'           => 'servicioalcliente@favorita.com',
                'telefono'        => '022996100',
                'direccion'       => 'Av. General Enríquez S/N y Vía Cotogchoa, Sangolquí',
                'canton'          => 'Rumiñahui',
                'is_active'        => true,
            ],
            [
                'ruc'             => '1790142663001',
                'razon_social'    => 'AGROPESA INDUSTRIA AGROPECUARIA ECUATORIANA SA',
                'nombre_comercial' => 'Agropesa',
                'actividad'       => 'Procesamiento y distribución de productos cárnicos y perecibles',
                'email'           => 'pedidos@agropesa.com.ec',
                'telefono'        => '022870340',
                'direccion'       => 'Vía a Amaguaña Km 3, Sector San Rafael, Pichincha',
                'canton'          => 'Rumiñahui',
                'is_active'        => true,
            ],
            [
                'ruc'             => '1790971937001',
                'razon_social'    => 'PROVEFRUT S.A.',
                'nombre_comercial' => 'Provefrut',
                'actividad'       => 'Procesamiento y comercialización de frutas y vegetales congelados',
                'email'           => 'info@provefrut.com',
                'telefono'        => '022380100',
                'direccion'       => 'Av. Interoceánica Km 14.5 y Escalón Tumbaco, Quito',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],

            // ==================== SERVICIOS, VEHÍCULOS Y REPUESTOS ====================
            [
                'ruc'             => '1790598012001',
                'razon_social'    => 'GENERAL MOTORS DEL ECUADOR S.A.',
                'nombre_comercial' => 'General Motors',
                'actividad'       => 'Ensamblaje, venta de vehículos comerciales, camiones y repuestos originales',
                'email'           => 'comercial@chevrolet.com.ec',
                'telefono'        => '022998000',
                'direccion'       => 'Av. Panamericana Norte Km 5.5 y Capitán Giovanni Calles, Quito',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],
            [
                'ruc'             => '1792339561001',
                'razon_social'    => 'PROVEREPUESTOS CIA. LTDA.',
                'nombre_comercial' => 'Proverepuestos',
                'actividad'       => 'Distribución de repuestos automotrices para flotas de transporte',
                'email'           => 'ventas@proverepuestos.com.ec',
                'telefono'        => '022612345',
                'direccion'       => 'Av. Mariscal Sucre S12-45 y Rodrigo de Chávez, Quito',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],
            [
                'ruc'             => '1791289927001',
                'razon_social'    => 'SEGUROS DEL PICHINCHA S.A. COMPAÑIA DE SEGUROS Y REASEGUROS',
                'nombre_comercial' => 'Seguros del Pichincha',
                'actividad'       => 'Servicios de pólizas de seguro de bienes, vehículos y responsabilidad civil',
                'email'           => 'servicio@segurosdelpichincha.com',
                'telefono'        => '023982000',
                'direccion'       => 'Av. 12 de Octubre N24-562 y Cordero, Parroquia Mariscal Sucre, Quito',
                'canton'          => 'Quito',
                'is_active'        => true,
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::firstOrCreate(
                ['ruc' => $proveedor['ruc']],
                [
                    'razon_social'     => $proveedor['razon_social'],
                    'nombre_comercial' => $proveedor['nombre_comercial'],
                    'actividad'        => $proveedor['actividad'],
                    'email'            => $proveedor['email'],
                    'telefono'         => $proveedor['telefono'],
                    'direccion'        => $proveedor['direccion'],
                    'canton'           => $proveedor['canton'],
                    'is_active'        => $proveedor['is_active'],
                ]
            );
        }

        $this->command->info('Seeder de Proveedores de Pichincha ejecutado con éxito.');
    }
}

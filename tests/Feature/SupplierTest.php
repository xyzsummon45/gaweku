<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_be_created_and_searched(): void
    {
        $this->post('/supplier', [
            'nama_supplier' => 'CV Pasir Jaya',
            'no_hp' => '08123456789',
            'alamat' => 'Jakarta',
            'catatan' => 'Supplier pasir dan abu batu',
        ])->assertRedirect('/supplier');

        $this->assertDatabaseHas('suppliers', [
            'nama_supplier' => 'CV Pasir Jaya',
            'no_hp' => '08123456789',
        ]);

        Supplier::create([
            'nama_supplier' => 'Toko Semen Abadi',
            'no_hp' => '0800000000',
        ]);

        $this->get('/supplier?q=pasir')
            ->assertOk()
            ->assertSee('CV Pasir Jaya')
            ->assertDontSee('Toko Semen Abadi');
    }
}

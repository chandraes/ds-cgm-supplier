<?php

namespace Tests\Unit;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerControllerTest extends TestCase
{
    // use RefreshDatabase;

    public function testStore()
    {
        $data = [
            'nama' => 'Test',
            'npwp' => '123456789012345',
            'singkatan' => 'T',
            'cp' => 'Test CP',
            'no_wa' => '1234567890',
            'alamat' => 'Test Address',
        ];

        // Validate the data
        $validator = Validator::make($data, [
            'nama' => 'required',
            'npwp' => 'required',
            'singkatan' => 'required',
            'cp' => 'required',
            'no_wa' => 'required',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            $this->fail('Request validation failed');
        }

        $request = new Request($data);

        $controller = new \App\Http\Controllers\CustomerController();
        $controller->store($request);

        $this->assertDatabaseHas('customers', $data);
    }
}

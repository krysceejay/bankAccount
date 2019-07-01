<?php

namespace Tests\Feature;
use App\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BalanceTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_get_balance()
    {

      // $user = factory(User::class)->create(['id'=>1, 'balance' => 50]);

      // dd($user);


      dd($this->get('/api/balance/1'));
      // ->assertStatus(200)
      // ->assertJson([
      //       'status' => 'success',
      //       'status code' => 200,
      //       'data' => 'USD50'
      //     ]);
     // ->assertEquals(50, $user->balance);

    }
}

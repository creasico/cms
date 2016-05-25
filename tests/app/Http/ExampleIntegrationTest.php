<?php

namespace App\Tests\Http;

use App\Tests\IntegrationTestCase;
// use Illuminate\Foundation\Testing\WithoutMiddleware;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleIntegrationTest extends IntegrationTestCase
{
    /**
     * A basic functional test example.
     *
     * @group integration
     */
    public function testBasicExample()
    {
        // $this->webDriver->get($this->baseUrl);
        $this->webDriver->get('http://creasi.co');

        $this->assertEquals('Welcome to nginx!', $this->webDriver->getTitle());

        $this->webDriver->close();
    }
}

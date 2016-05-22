<?php

namespace App\Tests\Database;

use Mockery;
use Carbon\Carbon;
use App\Tests\TestCase;
use App\Database\Model;
use App\Database\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

class BuilderTest extends TestCase
{
    use InteractsWithDatabase;

    /** @var Model */
    private $model;

    /** @var Builder */
    private $builder;

    public function setUp()
    {
        parent::setUp();

        $this->model = Mockery::mock(Model::class);
        $this->builder = Mockery::mock(Builder::class);
    }

    public function testFindingRecordByRouteKey()
    {
        $this->builder->shouldReceive('findByRouteKey')
                      ->withArgs(['id'])
                      ->andReturn($this->model);

        $this->assertInstanceOf(Model::class, $this->builder->findByRouteKey('id'));
    }
}

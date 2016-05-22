<?php

namespace App\Tests\Database;

use Mockery;
use Carbon\Carbon;
use App\Tests\TestCase;
use App\Database\Model;
use App\Database\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ModelTest extends TestCase
{
    /**
     * @var Model
     */
    private $mock;

    public function setUp()
    {
        parent::setUp();

        $this->mock = Mockery::mock(Model::class);
    }

    public function testGettingRouteKeyName()
    {
        $this->mock->shouldReceive('getRouteKeyName')->andReturn('id');

        $this->assertEquals('id', $this->mock->getRouteKeyName());
    }

    public function testSettingRouteKeyName()
    {
        $this->mock->shouldReceive('setRouteKeyName')->once();
        $this->mock->shouldReceive('getRouteKeyName')->andReturn('name');

        $this->mock->setRouteKeyName('name');
        $this->assertEquals('name', $this->mock->getRouteKeyName());
    }

    public function testGettingQualifiedRouteKeyName()
    {
        $this->mock->shouldReceive('getQualifiedRouteKeyName')->andReturn('model.id');

        $this->assertEquals('model.id', $this->mock->getQualifiedRouteKeyName());
    }

    public function testGettingSearchable()
    {
        $this->mock->shouldReceive('getSearchable')->andReturn([]);

        $this->assertEquals([], $this->mock->getSearchable());
    }

    public function testCreatingNewBuilder()
    {
        $builder = Mockery::mock(Builder::class);
        $queryBuilder = Mockery::mock(QueryBuilder::class);
        $this->mock->shouldReceive('newEloquentBuilder')->withArgs([$queryBuilder])->andReturn($builder);

        $this->assertInstanceOf(Builder::class, $this->mock->newEloquentBuilder($queryBuilder));
    }

    /**
     * @dataProvider dateStringValuesProvider
     */
    public function testCastingValueAsDateTime($value, $expected)
    {
        $carbon = Mockery::mock(Carbon::class);
        $carbon->shouldReceive('__toString')->andReturn($expected);
        $this->mock->shouldAllowMockingProtectedMethods();
        $this->mock->shouldReceive('asDateTime')->withArgs([$value])->andReturn($carbon);

        $actualReturn = $this->mock->asDateTime($value);
        $this->assertInstanceOf(Carbon::class, $actualReturn);
        $this->assertEquals($expected, (string) $actualReturn);
    }

    public function dateStringValuesProvider()
    {
        $now = new Carbon();
        $expect = (string) $now->format('d F Y, H:i');

        return [
            [$now, $expect],
            [$now->timestamp, $expect],
            [$now->toDateString(), $expect],
            [$now->toDateTimeString(), $expect],
        ];
    }
}

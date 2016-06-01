<?php

namespace App\Tests\Database;

use Mockery;
use Carbon\Carbon;
use App\Tests\TestCase;
use App\Database\Model as AppModel;
use App\Database\Builder as AppBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ModelTest extends TestCase
{
    public function testGettingRouteKeyName()
    {
        $model = new Model;

        $this->assertEquals('id', $model->getRouteKeyName());

        $this->assertEquals('models.id', $model->getQualifiedRouteKeyName());

        $model->setRouteKeyName('slug');
        $this->assertEquals('slug', $model->getRouteKeyName());

        $this->assertEquals('models.slug', $model->getQualifiedRouteKeyName());
    }

    public function testGettingSearchable()
    {
        $model = new Model;

        $this->assertEquals([], $model->getSearchable());
    }

    /**
     * @dataProvider dateStringValuesProvider
     */
    public function testCastingValueAsDateTime($value, $expected)
    {
        $mock = $this->getMockForAbstractClass(AppModel::class);
        $mock->method('asDateTime')->with($value);

        $model = new \ReflectionMethod(Model::class, 'asDateTime');
        $model->setAccessible(true);

        $actualReturn = $model->invokeArgs($mock, [$value]);
        $this->assertInstanceOf(Carbon::class, $actualReturn);
        $this->assertEquals($expected, (string) $actualReturn);
    }

    public function dateStringValuesProvider()
    {
        $now = new Carbon(null, 'Asia/Jakarta');
        $expect = (string) $now;

        return [
            [$now, $expect],
            [$now->timestamp, $expect],
            [$now->toDateTimeString(), $expect],
            [$now->format('d F Y, H:i:s'), $expect],
        ];
    }
}

class Model extends AppModel
{}

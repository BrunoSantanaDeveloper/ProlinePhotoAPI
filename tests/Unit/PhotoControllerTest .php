<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\PhotoController;
use Illuminate\Http\Request;
use Mockery;
use App\Models\Photo;
use App\Services\PhotoService;

class PhotoControllerTest extends TestCase
{
    protected $service;
    protected $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = Mockery::mock(PhotoService::class);
        $this->controller = new PhotoController($this->service);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testIndex()
    {
        $photos = Photo::factory()->count(3)->make();

        $this->service->shouldReceive('getAllPhotos')->once()->andReturn($photos);

        $response = $this->controller->index();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($photos->toArray(), $response->getData(true));
    }

    public function testShow()
    {
        $photo = Photo::factory()->make();

        $this->service->shouldReceive('getPhotoById')->with(1)->once()->andReturn($photo);

        $response = $this->controller->show(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($photo->toArray(), $response->getData(true));
    }

    public function testStore()
    {
        $photoData = ['photo' => 'photo.jpg', 'latitude' => 40.7128, 'longitude' => -74.0060];
        $photo = new Photo($photoData);

        $this->service->shouldReceive('createPhoto')->with($photoData)->once()->andReturn($photo);

        $request = Request::create('/photos', 'POST', $photoData);
        $response = $this->controller->store($request);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($photo->toArray(), $response->getData(true));
    }

    public function testUpdate()
    {
        $photoData = ['photo' => 'updated_photo.jpg', 'latitude' => 40.7128, 'longitude' => -74.0060];

        $this->service->shouldReceive('updatePhoto')->with(1, $photoData)->once()->andReturn(true);

        $request = Request::create('/photos/1', 'PUT', $photoData);
        $response = $this->controller->update($request, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true));
    }

    public function testDestroy()
    {
        $this->service->shouldReceive('deletePhoto')->with(1)->once()->andReturn(true);

        $response = $this->controller->destroy(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true));
    }

    public function testStoreWithInvalidData()
    {
        $photoData = ['photo' => '', 'latitude' => 'invalid', 'longitude' => 'invalid'];

        $this->service->shouldReceive('createPhoto')
            ->with($photoData)
            ->once()
            ->andThrow(new \Illuminate\Validation\ValidationException(
                validator($photoData, [
                    'photo' => 'required|file|mimes:jpeg,png,jpg|max:2048',
                    'latitude' => 'required|numeric',
                    'longitude' => 'required|numeric'
                ])
            ));

        $request = Request::create('/photos', 'POST', $photoData);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->controller->store($request);
    }
}

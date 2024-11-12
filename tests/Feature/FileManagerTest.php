<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use Illuminate\Support\Facades\File;
use Mockery;

class FileManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up a local temporary disk for testing
        Storage::fake('test_disk');
    }

    /** @test */
    public function it_returns_empty_arrays_when_directory_is_empty()
    {
        $fileManager = new \App\Services\FileManagerService(); // Replace with actual service path

        $response = $fileManager->listContents('', false);

        $this->assertTrue($response['success']);
        $this->assertEmpty($response['data']['files']);
        $this->assertEmpty($response['data']['folders']);
    }
}

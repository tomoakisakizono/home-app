<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MessageRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
    /** @test */
    public function バリデーションが成功する場合()
    {
        $data = [
            'content' => 'これはテストメッセージ',
            'event_date' => '2025-08-01',
            'event_time' => '12:00',
            'event_title' => '予定名',
            'event_description' => '詳細説明',
        ];

        $request = new MessageRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function contentが空だとバリデーションエラーになる()
    {
        $data = [
            'content' => '',
        ];

        $request = new MessageRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('content', $validator->errors()->toArray());
    }
}

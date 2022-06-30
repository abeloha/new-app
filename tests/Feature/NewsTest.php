<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_access()
    {
        $response = $this->get('api/v1/news');
        $response->assertStatus(401);
    }

    public function test_user_can_create_news()
    {
        $news = News::factory()->make();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('api/v1/news', [
            'title' => $news->title,
            'content' => $news->content,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'content',
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJsonPath('data.title', $news->title);
    }

    public function test_user_can_update_news()
    {
        $news = News::factory()->create();
        $user = User::factory()->create();

        $updatedTitle = $news->title;

        $response = $this->actingAs($user)->putJson('api/v1/news/'.$news->id, [
            'title' => $updatedTitle,
            'content' => $news->content,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'content',
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJsonPath('data.title', $updatedTitle);
    }

    public function test_user_can_get_news()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('api/v1/news');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'content',
                    'created_at',
                    'updated_at',
               ]
            ],
            'links',
            'meta',
        ]);

    }

    public function test_user_can_view_news()
    {
        $user = User::factory()->create();
        $news = News::factory()->create();

        $response = $this->actingAs($user)->getJson('api/v1/news/'.$news->id,);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'content',
                'created_at',
                'updated_at',
                'user' => [
                    'id',
                    'name',
                ],
            ],
        ]);

    }
}

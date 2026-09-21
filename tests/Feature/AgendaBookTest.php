<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\SuratUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Buku Agenda (AgendaBookController) — arsitektur.md §11 Fase 2. */
class AgendaBookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_letters_are_grouped_by_type_and_sorted_by_agenda_number(): void
    {
        $admin = SuratUser::factory()->admin()->create(['must_change_password' => false]);
        $creator = SuratUser::factory()->create();

        Letter::create(['letter_type' => 'I', 'agenda_no' => '10', 'letter_no' => 'A', 'subject' => 'Kesepuluh', 'received_date' => '2026-05-01', 'created_by' => $creator->id]);
        Letter::create(['letter_type' => 'I', 'agenda_no' => '2', 'letter_no' => 'B', 'subject' => 'Kedua', 'received_date' => '2026-01-01', 'created_by' => $creator->id]);
        Letter::create(['letter_type' => 'II', 'agenda_no' => '1', 'letter_no' => 'C', 'subject' => 'Jenis II', 'received_date' => '2026-03-01', 'created_by' => $creator->id]);

        $response = $this->actingAs($admin)->get(route('agenda-book.index', ['year' => 2026]));

        $response->assertOk();
        // Nomor agenda diurutkan numerik (2 sebelum 10), bukan alfabetis.
        $response->assertSeeInOrder(['Jenis I', 'B', 'A', 'Jenis II', 'C']);
    }

    public function test_only_shows_letters_within_the_users_visibility_scope(): void
    {
        $owner = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $other = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        Letter::create(['letter_type' => 'I', 'agenda_no' => '1', 'letter_no' => 'OWN', 'subject' => 'Punya sendiri', 'received_date' => '2026-01-01', 'created_by' => $owner->id]);
        Letter::create(['letter_type' => 'I', 'agenda_no' => '2', 'letter_no' => 'OTHER', 'subject' => 'Punya orang lain', 'received_date' => '2026-01-02', 'created_by' => $other->id]);

        $response = $this->actingAs($owner)->get(route('agenda-book.index', ['year' => 2026]));

        $response->assertOk();
        $response->assertSee('OWN');
        $response->assertDontSee('OTHER');
    }
}

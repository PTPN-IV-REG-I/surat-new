<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\SuratUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Laporan evaluasi tindak lanjut (ReportController) — Fase 3, arsitektur.md §11. */
class FollowUpReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_only_shows_letters_not_yet_followed_up_and_counts_overdue_ones(): void
    {
        $admin = SuratUser::factory()->admin()->create(['must_change_password' => false]);
        $creator = SuratUser::factory()->create();

        Letter::create(['letter_type' => 'I', 'agenda_no' => '1', 'letter_no' => 'OVERDUE', 'subject' => 'Sudah lama', 'received_date' => now()->subDays(10), 'follow_up' => false, 'created_by' => $creator->id]);
        Letter::create(['letter_type' => 'I', 'agenda_no' => '2', 'letter_no' => 'FRESH', 'subject' => 'Baru masuk', 'received_date' => now()->subDays(2), 'follow_up' => false, 'created_by' => $creator->id]);
        Letter::create(['letter_type' => 'I', 'agenda_no' => '3', 'letter_no' => 'DONE', 'subject' => 'Sudah selesai', 'received_date' => now()->subDays(10), 'follow_up' => true, 'created_by' => $creator->id]);

        $response = $this->actingAs($admin)->get(route('reports.follow-up'));

        $response->assertOk();
        $response->assertSee('OVERDUE');
        $response->assertSee('FRESH');
        $response->assertDontSee('DONE');
    }

    public function test_respects_the_same_visibility_scope_as_the_archive(): void
    {
        $owner = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $other = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        Letter::create(['letter_type' => 'I', 'agenda_no' => '1', 'letter_no' => 'MINE', 'subject' => 'Punya sendiri', 'received_date' => now()->subDays(10), 'follow_up' => false, 'created_by' => $owner->id]);
        Letter::create(['letter_type' => 'I', 'agenda_no' => '2', 'letter_no' => 'NOTMINE', 'subject' => 'Punya orang lain', 'received_date' => now()->subDays(10), 'follow_up' => false, 'created_by' => $other->id]);

        $response = $this->actingAs($owner)->get(route('reports.follow-up'));

        $response->assertOk();
        $response->assertSee('MINE');
        $response->assertDontSee('NOTMINE');
    }
}

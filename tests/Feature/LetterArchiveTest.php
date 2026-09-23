<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Letter;
use App\Models\SuratUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Arsip surat read-only (LetterController) — memastikan scoping per role
 * (arsitektur.md §10) benar: garden-officer hanya lihat suratnya sendiri,
 * department-head hanya yang jadi tujuan bagiannya, admin lihat semua.
 */
class LetterArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_garden_officer_only_sees_own_letters(): void
    {
        $owner = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $other = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        $ownLetter = $this->makeLetter(['created_by' => $owner->id, 'letter_no' => 'OWN/001']);
        $otherLetter = $this->makeLetter(['created_by' => $other->id, 'letter_no' => 'OTHER/001']);

        $this->actingAs($owner)->get(route('letters.index'))->assertOk();

        $response = $this->actingAs($owner)->postJson(route('letters.data'));

        $response->assertOk();
        $response->assertJsonFragment(['letter_no' => 'OWN/001']);
        $response->assertJsonMissing(['letter_no' => 'OTHER/001']);

        $this->actingAs($owner)->get(route('letters.show', $ownLetter))->assertOk();
        $this->actingAs($owner)->get(route('letters.show', $otherLetter))->assertNotFound();
    }

    public function test_department_head_only_sees_letters_addressed_to_their_department(): void
    {
        $department = Department::create(['legacy_flag' => 'B1', 'code' => 'BSDM', 'name' => 'Biro SDM']);
        $otherDepartment = Department::create(['legacy_flag' => 'B2', 'code' => 'BUMU', 'name' => 'Biro Umum']);

        $head = SuratUser::factory()->departmentHead()->create([
            'department_id' => $department->id,
            'must_change_password' => false,
        ]);

        $addressed = $this->makeLetter(['letter_no' => 'DEPT/001']);
        $addressed->departmentRecipients()->attach($department->id);

        $notAddressed = $this->makeLetter(['letter_no' => 'DEPT/002']);
        $notAddressed->departmentRecipients()->attach($otherDepartment->id);

        $response = $this->actingAs($head)->postJson(route('letters.data'));

        $response->assertOk();
        $response->assertJsonFragment(['letter_no' => 'DEPT/001']);
        $response->assertJsonMissing(['letter_no' => 'DEPT/002']);
    }

    public function test_admin_sees_all_letters(): void
    {
        $admin = SuratUser::factory()->admin()->create(['must_change_password' => false]);

        $this->makeLetter(['letter_no' => 'ALL/001']);
        $this->makeLetter(['letter_no' => 'ALL/002']);

        $response = $this->actingAs($admin)->postJson(route('letters.data'));

        $response->assertOk();
        $response->assertJsonFragment(['letter_no' => 'ALL/001']);
        $response->assertJsonFragment(['letter_no' => 'ALL/002']);
    }

    public function test_search_filters_by_letter_no_and_subject(): void
    {
        $admin = SuratUser::factory()->admin()->create(['must_change_password' => false]);

        $this->makeLetter(['letter_no' => 'SEARCH/001', 'subject' => 'Undangan Rapat']);
        $this->makeLetter(['letter_no' => 'SEARCH/002', 'subject' => 'Laporan Bulanan']);

        $response = $this->actingAs($admin)->postJson(route('letters.data'), ['search' => ['value' => 'Undangan']]);

        $response->assertOk();
        $response->assertJsonFragment(['letter_no' => 'SEARCH/001']);
        $response->assertJsonMissing(['letter_no' => 'SEARCH/002']);
    }

    public function test_date_filter_by_range_matches_letters_within_period(): void
    {
        $admin = SuratUser::factory()->admin()->create(['must_change_password' => false]);

        $this->makeLetter([
            'letter_no' => 'DATE/AUG',
            'received_date' => '2026-08-15',
            'letter_date' => '2026-08-14',
        ]);
        $this->makeLetter([
            'letter_no' => 'DATE/SEP',
            'received_date' => '2026-09-09',
            'letter_date' => '2026-09-09',
        ]);
        $this->makeLetter([
            'letter_no' => 'DATE/OCT',
            'received_date' => '2026-10-15',
            'letter_date' => '2026-10-14',
        ]);

        $response = $this->actingAs($admin)->get(route('letters.index', ['date' => '2026-08-01 - 2026-09-21']));

        $response->assertOk();
        $response->assertSee('DATE/AUG');
        $response->assertSee('DATE/SEP');
        $response->assertDontSee('DATE/OCT');

        $dataResponse = $this->actingAs($admin)->postJson(route('letters.data', ['date' => '2026-08-01 - 2026-09-21']));
        $dataResponse->assertOk();
        $dataResponse->assertJsonFragment(['letter_no' => 'DATE/AUG']);
        $dataResponse->assertJsonFragment(['letter_no' => 'DATE/SEP']);
        $dataResponse->assertJsonMissing(['letter_no' => 'DATE/OCT']);
    }

    private function makeLetter(array $overrides = []): Letter
    {
        $creator = $overrides['created_by'] ?? SuratUser::factory()->gardenOfficer()->create()->id;

        return Letter::create(array_merge([
            'letter_type' => 'I',
            'agenda_no' => '1',
            'agenda_series' => '01',
            'letter_no' => 'TEST/'.uniqid(),
            'subject' => 'Hal Uji Coba',
            'letter_date' => now()->subDays(2),
            'received_date' => now()->subDay(),
            'follow_up' => false,
            'created_by' => $creator,
        ], $overrides));
    }
}

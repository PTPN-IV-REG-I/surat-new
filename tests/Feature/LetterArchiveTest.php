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

        $response = $this->actingAs($owner)->get(route('letters.index'));

        $response->assertOk();
        $response->assertSee('OWN/001');
        $response->assertDontSee('OTHER/001');

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

        $response = $this->actingAs($head)->get(route('letters.index'));

        $response->assertOk();
        $response->assertSee('DEPT/001');
        $response->assertDontSee('DEPT/002');
    }

    public function test_admin_sees_all_letters(): void
    {
        $admin = SuratUser::factory()->admin()->create(['must_change_password' => false]);

        $this->makeLetter(['letter_no' => 'ALL/001']);
        $this->makeLetter(['letter_no' => 'ALL/002']);

        $response = $this->actingAs($admin)->get(route('letters.index'));

        $response->assertOk();
        $response->assertSee('ALL/001');
        $response->assertSee('ALL/002');
    }

    public function test_search_filters_by_letter_no_and_subject(): void
    {
        $admin = SuratUser::factory()->admin()->create(['must_change_password' => false]);

        $this->makeLetter(['letter_no' => 'SEARCH/001', 'subject' => 'Undangan Rapat']);
        $this->makeLetter(['letter_no' => 'SEARCH/002', 'subject' => 'Laporan Bulanan']);

        $response = $this->actingAs($admin)->get(route('letters.index', ['q' => 'Undangan']));

        $response->assertOk();
        $response->assertSee('SEARCH/001');
        $response->assertDontSee('SEARCH/002');
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

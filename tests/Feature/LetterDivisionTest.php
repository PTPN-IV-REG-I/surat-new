<?php

namespace Tests\Feature;

use App\Models\DispositionType;
use App\Models\LetterDivision;
use App\Models\SuratUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Surat Bagian (LetterDivisionController) — Fase 3, arsitektur.md §11.
 */
class LetterDivisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_department_head_can_create_a_division_letter_with_an_agenda_number(): void
    {
        $head = SuratUser::factory()->departmentHead()->create(['must_change_password' => false]);

        $response = $this->actingAs($head)->post(route('letter-divisions.store'), [
            'agenda_type_code' => 'sp-iii',
            'subject' => 'Permohonan data',
            'received_date' => '2026-09-20',
        ]);

        $division = LetterDivision::where('subject', 'Permohonan data')->firstOrFail();

        $response->assertRedirect(route('letter-divisions.show', $division));
        $this->assertSame(1, $division->agenda_no);
        $this->assertSame('SP-III', $division->agenda_type_code);
        $this->assertSame($head->id, $division->created_by);
    }

    public function test_garden_officer_cannot_access_letter_divisions(): void
    {
        $officer = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        $this->actingAs($officer)->get(route('letter-divisions.index'))->assertForbidden();
        $this->actingAs($officer)->get(route('letter-divisions.create'))->assertForbidden();
    }

    public function test_department_head_can_add_disposition_to_a_division_letter(): void
    {
        $head = SuratUser::factory()->departmentHead()->create(['must_change_password' => false]);
        $type = DispositionType::create(['code' => 'JAWAB', 'label' => 'Jawab', 'sort_order' => 1]);

        $division = LetterDivision::create([
            'agenda_type_code' => 'SP-III',
            'agenda_no' => 1,
            'subject' => 'Hal uji disposisi',
            'created_by' => $head->id,
        ]);

        $response = $this->actingAs($head)->post(route('letter-divisions.dispositions.store', $division), [
            'disposition_type_ids' => [$type->id],
            'note' => 'Segera dijawab',
        ]);

        $response->assertRedirect(route('letter-divisions.show', $division));

        $disposition = $division->fresh()->dispositions()->first();
        $this->assertSame('department-head', $disposition->actor_role);
        $this->assertSame($head->id, $disposition->created_by);
    }
}

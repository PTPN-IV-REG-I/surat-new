<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\DispositionType;
use App\Models\Letter;
use App\Models\SuratUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Instruksi disposisi (LetterDispositionController) — arsitektur.md §11
 * Fase 2 "form disposisi Sekretaris Direksi".
 */
class LetterDispositionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_owner_can_add_multiple_dispositions_at_once(): void
    {
        $owner = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $letter = $this->makeLetter(['created_by' => $owner->id]);

        $typeA = DispositionType::create(['code' => 'SELESAIKAN', 'label' => 'Selesaikan', 'sort_order' => 1]);
        $typeB = DispositionType::create(['code' => 'JAWAB', 'label' => 'Jawab', 'sort_order' => 2]);

        $response = $this->actingAs($owner)->post(route('letters.dispositions.store', $letter), [
            'disposition_type_ids' => [$typeA->id, $typeB->id],
            'note' => 'Mohon segera ditindaklanjuti',
        ]);

        $response->assertRedirect(route('letters.show', $letter));
        $this->assertCount(2, $letter->fresh()->dispositions);
        $this->assertSame($owner->id, $letter->dispositions()->first()->created_by);
    }

    public function test_department_head_cannot_dispose_main_letters(): void
    {
        $department = Department::create(['legacy_flag' => 'B1', 'code' => 'BSDM', 'name' => 'Biro SDM']);
        $head = SuratUser::factory()->departmentHead()->create([
            'department_id' => $department->id,
            'must_change_password' => false,
        ]);

        $letter = $this->makeLetter();
        $letter->departmentRecipients()->attach($department->id);

        $type = DispositionType::create(['code' => 'JAWAB', 'label' => 'Jawab', 'sort_order' => 1]);

        $this->actingAs($head)->post(route('letters.dispositions.store', $letter), [
            'disposition_type_ids' => [$type->id],
        ])->assertForbidden();
    }

    public function test_user_cannot_dispose_a_letter_outside_their_visibility_scope(): void
    {
        $officer = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $otherOfficer = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        $letter = $this->makeLetter(['created_by' => $otherOfficer->id]);
        $type = DispositionType::create(['code' => 'JAWAB', 'label' => 'Jawab', 'sort_order' => 1]);

        $this->actingAs($officer)->post(route('letters.dispositions.store', $letter), [
            'disposition_type_ids' => [$type->id],
        ])->assertNotFound();
    }

    public function test_disposition_creator_can_delete_their_own_entry(): void
    {
        $owner = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $letter = $this->makeLetter(['created_by' => $owner->id]);
        $type = DispositionType::create(['code' => 'JAWAB', 'label' => 'Jawab', 'sort_order' => 1]);

        $this->actingAs($owner)->post(route('letters.dispositions.store', $letter), [
            'disposition_type_ids' => [$type->id],
        ]);

        $disposition = $letter->dispositions()->first();

        $this->actingAs($owner)
            ->delete(route('letters.dispositions.destroy', [$letter, $disposition]))
            ->assertRedirect(route('letters.show', $letter));

        $this->assertDatabaseMissing('letter_dispositions', ['id' => $disposition->id]);
    }

    private function makeLetter(array $overrides = []): Letter
    {
        $creator = $overrides['created_by'] ?? SuratUser::factory()->gardenOfficer()->create()->id;

        return Letter::create(array_merge([
            'letter_type' => 'I',
            'agenda_no' => '1',
            'letter_no' => 'DISP/'.uniqid(),
            'subject' => 'Hal Uji Coba Disposisi',
            'received_date' => now()->subDay(),
            'created_by' => $creator,
        ], $overrides));
    }
}

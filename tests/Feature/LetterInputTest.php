<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Director;
use App\Models\Letter;
use App\Models\SuratUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Input/edit surat (Kebun) — LetterController@create/store/edit/update.
 * Lihat arsitektur.md §11 Fase 2.
 */
class LetterInputTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_garden_officer_can_create_a_letter_and_gets_an_agenda_number(): void
    {
        $user = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $director = Director::create(['code' => '1', 'name' => 'DIRPEL']);

        $response = $this->actingAs($user)->post(route('letters.store'), [
            'letter_type' => 'I',
            'letter_no' => 'SRT/001/2026',
            'director_id' => $director->id,
            'subject' => 'Undangan Rapat Koordinasi',
            'received_date' => '2026-09-20',
            'sender_name' => 'Kantor Pusat',
        ]);

        $letter = Letter::where('letter_no', 'SRT/001/2026')->firstOrFail();

        $response->assertRedirect(route('letters.show', $letter));
        $this->assertSame('1', $letter->agenda_no);
        $this->assertSame($user->id, $letter->created_by);
        $this->assertFalse($letter->follow_up);
    }

    public function test_second_letter_of_the_same_type_and_year_gets_the_next_agenda_number(): void
    {
        $user = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        $this->actingAs($user)->post(route('letters.store'), [
            'letter_type' => 'I',
            'letter_no' => 'SRT/001/2026',
            'subject' => 'Surat pertama',
            'received_date' => '2026-09-20',
        ]);

        $this->actingAs($user)->post(route('letters.store'), [
            'letter_type' => 'I',
            'letter_no' => 'SRT/002/2026',
            'subject' => 'Surat kedua',
            'received_date' => '2026-09-21',
        ]);

        $this->assertSame('2', Letter::where('letter_no', 'SRT/002/2026')->value('agenda_no'));
    }

    public function test_director_and_department_recipients_are_saved(): void
    {
        $user = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $director = Director::create(['code' => '2', 'name' => 'DIRPROD']);
        $department = Department::create(['legacy_flag' => 'B1', 'code' => 'BSDM', 'name' => 'Biro SDM']);

        $this->actingAs($user)->post(route('letters.store'), [
            'letter_type' => 'II',
            'letter_no' => 'SRT/CC/2026',
            'subject' => 'Surat dengan tembusan',
            'received_date' => '2026-09-20',
            'director_recipients' => [$director->id],
            'department_recipients' => [$department->id],
        ]);

        $letter = Letter::where('letter_no', 'SRT/CC/2026')->firstOrFail();

        $this->assertTrue($letter->directorRecipients->contains($director));
        $this->assertTrue($letter->departmentRecipients->contains($department));
    }

    public function test_garden_officer_cannot_edit_another_users_letter(): void
    {
        $owner = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $intruder = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        $letter = Letter::create([
            'letter_type' => 'I',
            'agenda_no' => '1',
            'letter_no' => 'PROTECTED/001',
            'subject' => 'Rahasia',
            'received_date' => '2026-09-20',
            'created_by' => $owner->id,
        ]);

        $this->actingAs($intruder)->get(route('letters.edit', $letter))->assertForbidden();
        $this->actingAs($intruder)->put(route('letters.update', $letter), [
            'letter_no' => 'HACKED/001',
            'subject' => 'Diubah paksa',
        ])->assertForbidden();

        $this->assertSame('Rahasia', $letter->fresh()->subject);
    }

    public function test_department_head_cannot_create_letters(): void
    {
        $head = SuratUser::factory()->departmentHead()->create(['must_change_password' => false]);

        $this->actingAs($head)->get(route('letters.create'))->assertForbidden();
    }

    public function test_owner_can_update_their_own_letter(): void
    {
        $owner = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        $letter = Letter::create([
            'letter_type' => 'I',
            'agenda_no' => '1',
            'letter_no' => 'EDIT/001',
            'subject' => 'Judul lama',
            'received_date' => '2026-09-20',
            'created_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->put(route('letters.update', $letter), [
            'letter_no' => 'EDIT/001',
            'subject' => 'Judul baru',
            'received_date' => '2026-09-20',
        ]);

        $response->assertRedirect(route('letters.show', $letter));
        $this->assertSame('Judul baru', $letter->fresh()->subject);
        $this->assertSame('1', $letter->fresh()->agenda_no, 'agenda_no tidak boleh berubah saat edit');
    }
}

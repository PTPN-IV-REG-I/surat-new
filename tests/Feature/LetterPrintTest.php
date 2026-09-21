<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\SuratUser;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Cetak lembar disposisi (LetterController@print) — Fase 3, arsitektur.md §11. */
class LetterPrintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_owner_can_print_their_own_letter(): void
    {
        $owner = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $letter = Letter::create([
            'letter_type' => 'I',
            'agenda_no' => '1',
            'letter_no' => 'PRINT/001',
            'subject' => 'Hal cetak',
            'received_date' => '2026-09-20',
            'created_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->get(route('letters.print', $letter));

        $response->assertOk();
        $response->assertSee('LEMBAR DISPOSISI');
        $response->assertSee('PRINT/001');
    }

    public function test_user_cannot_print_a_letter_outside_their_visibility_scope(): void
    {
        $officer = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);
        $other = SuratUser::factory()->gardenOfficer()->create(['must_change_password' => false]);

        $letter = Letter::create([
            'letter_type' => 'I',
            'agenda_no' => '1',
            'letter_no' => 'PRIVATE/001',
            'subject' => 'Rahasia',
            'received_date' => '2026-09-20',
            'created_by' => $other->id,
        ]);

        $this->actingAs($officer)->get(route('letters.print', $letter))->assertNotFound();
    }
}

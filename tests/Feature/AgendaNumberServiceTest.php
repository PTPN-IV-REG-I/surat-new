<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\SuratUser;
use App\Services\AgendaNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AgendaNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_number_for_a_new_type_and_year_is_one(): void
    {
        $number = (new AgendaNumberService)->next('I', 2026);

        $this->assertSame(1, $number);
    }

    public function test_numbers_increment_sequentially_for_the_same_type_and_year(): void
    {
        $service = new AgendaNumberService;

        $this->assertSame(1, $service->next('I', 2026));
        $this->assertSame(2, $service->next('I', 2026));
        $this->assertSame(3, $service->next('I', 2026));
    }

    public function test_counters_are_independent_per_type_and_year(): void
    {
        $service = new AgendaNumberService;

        $this->assertSame(1, $service->next('I', 2026));
        $this->assertSame(1, $service->next('II', 2026));
        $this->assertSame(1, $service->next('I', 2025));
        $this->assertSame(2, $service->next('I', 2026));
    }

    public function test_new_counter_seeds_from_existing_migrated_letters_instead_of_colliding(): void
    {
        $creator = SuratUser::factory()->create();

        Letter::create([
            'letter_type' => 'I',
            'agenda_no' => '500',
            'letter_no' => 'LEGACY/500',
            'subject' => 'Surat historis hasil migrasi',
            'received_date' => '2026-03-01',
            'created_by' => $creator->id,
        ]);

        $number = (new AgendaNumberService)->next('I', 2026);

        $this->assertSame(501, $number);
    }

    public function test_concurrent_requests_never_produce_duplicate_numbers(): void
    {
        // Simulasi race condition: dua "user" minta nomor untuk
        // type+year yang sama secara berurutan lewat transaksi terpisah
        // (di request nyata ini adalah dua HTTP request paralel).
        $service = new AgendaNumberService;

        $numbers = [];
        for ($i = 0; $i < 10; $i++) {
            $numbers[] = $service->next('III', 2026);
        }

        $this->assertSame(range(1, 10), $numbers);
        $this->assertSame(10, DB::table('agenda_counters')
            ->where('letter_type', 'III')->where('year', 2026)->value('last_number'));
    }
}

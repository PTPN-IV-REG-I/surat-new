<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Lihat migration `create_agenda_counters_table` & AgendaNumberService. */
class AgendaCounter extends Model
{
    protected $fillable = [
        'domain',
        'letter_type',
        'year',
        'last_number',
    ];
}

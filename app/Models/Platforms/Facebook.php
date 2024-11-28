<?php

namespace App\Models\Platforms;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;

class Facebook extends Model
{
    protected $fillable = [
        'page_id',
    ];

    public function accounts()
    {
        return $this->morphMany(Account::class, 'sociable');
    }
}

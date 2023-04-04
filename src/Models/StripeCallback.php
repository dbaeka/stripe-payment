<?php

namespace Dbaeka\StripePayment\Models;

use Illuminate\Database\Eloquent\Model;

class StripeCallback extends Model
{
    /** @var array<int, string>  */
    protected $fillable = ['charge_id', 'response', 'status'];

    /** @var array<string, string> */
    protected $casts = [
        'response' => 'array',
    ];
}

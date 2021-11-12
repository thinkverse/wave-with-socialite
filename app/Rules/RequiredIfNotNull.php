<?php

namespace App\Rules;

use Illuminate\Validation\Rules\RequiredIf;

class RequiredIfNotNull extends RequiredIf
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(! is_null(auth()->user()->password));
    }

}

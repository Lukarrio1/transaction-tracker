<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TrueOrFalseRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }
    public $custom_value;
    public function __construct($custom_value)
    {
        $this->custom_value = $custom_value;
    }

     public function passes($attribute, $value)
    {
        return $this->custom_value == true;
    }

    public function message()
    {
        return 'The database table selected does not match the fields presented in the csv file.';
    }
}

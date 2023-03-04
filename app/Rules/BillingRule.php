<?php

namespace App\Rules;

use App\Http\FPLib\Settings;
use Illuminate\Contracts\Validation\Rule;

class BillingRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $seatPlan = Settings::currentSeatPlan();
        if (in_array($value, $seatPlan)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans("validation.billing_rule_group_not_at_table");
    }
}

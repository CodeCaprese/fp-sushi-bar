<?php

namespace App\Http\FPLib;

use App\Http\Controllers\Metadata\MetadataController;

/**
 * Helper Class for the overall settings that are saved and read from database.
 */
class Settings
{
    /**
     * @param $numberOfSeats
     * @return mixed|true|null
     *
     * Get or set tje number of seats the table can hold.
     */
    static public function amountOfSeats($numberOfSeats = null)
    {
        $setting = "NUMBER_OF_SEATS";
        $default = 0;

        if (is_null($numberOfSeats)) {
            return MetadataController::getMetadata($setting, $default);
        } else {
            MetadataController::setMetadata($setting, $numberOfSeats);
            return true;
        }
    }

    /**
     * @param $takenSeats
     * @return mixed|true
     *
     * Get or set all taken seats at the table. These are set in an array.
     */
    static public function currentSeatPlan($takenSeats = null)
    {
        $setting = "SEAT_PLAN";
        $default = serialize(array_fill(0, self::amountOfSeats(), null));

        if (is_null($takenSeats)) {
            return unserialize(MetadataController::getMetadata($setting, $default));
        } else {
            MetadataController::setMetadata($setting, serialize($takenSeats));
            return true;
        }
    }

    /**
     * @param $groupNumber
     * @return mixed|true|null
     *
     * Get the last set group number for customer.
     * Set the newest group number of customer, that where seated at the table.
     */
    static public function groupNumber($groupNumber = null)
    {
        $setting = "GROUP_NUMBER";
        $default = 0;

        if (is_null($groupNumber)) {
            return MetadataController::getMetadata($setting, $default);
        } else {
            MetadataController::setMetadata($setting, $groupNumber);
            return true;
        }
    }
}

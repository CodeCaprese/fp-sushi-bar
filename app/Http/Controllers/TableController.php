<?php

namespace App\Http\Controllers;

use App\Http\FPLib\Settings;
use App\Http\Requests\BillingRequest;
use App\Http\Requests\SeatRequest;
use Illuminate\Http\Request;

/**
 *
 */
class TableController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * show table view with current seat plan
     */
    public function index()
    {
        $data = array();
        $data["numberOfSeats"] = Settings::amountOfSeats();
        $data["takenSeats"] = Settings::currentSeatPlan();

        return view("sites.table.index", ["data" => $data]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * customer has eaten and is asked to leave by paying the bill. Set entry with group number to null.
     */
    public function billing(BillingRequest $request)
    {
        $billedGroup = $request->get("group");
        $currentSeatPlan = Settings::currentSeatPlan();
        foreach ($currentSeatPlan as $key => $seat) {
            if ($seat == $billedGroup) {
                $currentSeatPlan[$key] = null;
            }
        }

        Settings::currentSeatPlan($currentSeatPlan);
        return redirect(route("table.index"))->with("success", trans("table.success_billing"));
    }

    /**
     * @param SeatRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * Uf seating is successful redirect with success message, otherwise redirect with error message.
     */
    public function seat(SeatRequest $request)
    {
        $customer = (int)$request->get("numberOfCustomers");
        if ($this->seatCustomer($customer)) {
            $msgType = "success";
            $message = trans("table.success_seating");
        } else {
            $msgType = "error";
            $message = trans("table.error_seating");
        }

        return redirect(route("table.index"))->with($msgType, $message);
    }

    /**
     * @param $amount
     * @return bool
     *
     * Set the group number in seat plan at optimize seat or return false if no empty seat group found.
     */
    public function seatCustomer($amount)
    {
        $amountOfSeats = Settings::amountOfSeats();
        $groupNumber = Settings::groupNumber();
        $currentSeatPlan = Settings::currentSeatPlan();

        $seats = $this->selectBestSeatingOption($this->getEmptySeatsInRow($currentSeatPlan), $amount);

        if ($seats === false) {
            // no seats in a group found
            return false;
        } else {
            $loopEnd = $seats["start"] + $amount;
            for ($i = $seats["start"]; $i < $loopEnd; $i++) {
                // Use modulo to simulate round table
                $currentSeatPlan[$i % $amountOfSeats] = $groupNumber;
            }
            Settings::currentSeatPlan($currentSeatPlan);
            Settings::groupNumber(++$groupNumber);

            return true;
        }

    }

    /**
     * @param $emptyGroupedSeats
     * @param $amount
     * @return array|false|mixed|void
     *
     * Check all the free seats for best option. If the needed amount of seats is found in a free space, seat them.
     * If multiple free spaces are found, check which space is the smallest.
     */
    private function selectBestSeatingOption($emptyGroupedSeats, $amount)
    {
        //No empty seats found, return false
        if (empty($emptyGroupedSeats)) {
            return false;
        }

        //There is only one possible empty group seat
        if (sizeof($emptyGroupedSeats, 1) == 3) {
            // reset key array
            $emptyGroupedSeats = array_values($emptyGroupedSeats);
            // Is empty group seat big enough for customer
            if ($emptyGroupedSeats[0]["seats"] >= $amount) {
                return [
                    "start" => $emptyGroupedSeats[0]["start"],
                    "seats" => $emptyGroupedSeats[0]["seats"]
                ];
            } else {
                return false;
            }
        }
        // Check all the empty group seats
        $possibleSeats = array();
        foreach ($emptyGroupedSeats as $seats) {
            // amount of free seats in group is to less
            if ($seats["seats"] < $amount) {
                continue;
            } else if ($seats["seats"] == $amount) {
                // necessary seats are equal to found free seats, return it, it fits perfect
                return $seats;
            } else if ($seats["seats"] > $amount) {
                // necessary seats are higher than found free seats, save them for later
                $possibleSeats[] = $seats;
            }
        }

        // There was no empty seats group, that fit perfect.

        if (empty($possibleSeats)) {
            // there is no possible option, return false
            return false;
        } else {
            // there are  options, check which has the less free seats to seat the group
            $optionToTake = null;
            // An empty group is always less than total amount of seats
            $prevDiff = Settings::amountOfSeats();
            foreach ($possibleSeats as $key => $option) {
                $diff = $option["seats"] - $amount; // seats that remains free
                if ($diff < $prevDiff) {
                    // this empty group is smaller, so better option
                    $optionToTake = $key;
                }
                $prevDiff = $diff;
            }
            return $possibleSeats[$optionToTake];
        }
    }

    /**
     * @param $currentSeatPlan
     * @return array|array[]|false
     *
     * Check in current seat plan, which seats in a group are free. If "last" seat and "first" seat are empty,
     * combine these to define a round table aka ring buffer
     */
    private function getEmptySeatsInRow($currentSeatPlan)
    {
        $seats = Settings::amountOfSeats();
        $emptySeats = array();

        // Table is completely full, no seats available
        if (empty(array_filter($currentSeatPlan, function ($a) {
            return $a === null;
        }))) {
            return false;
        }

        // Table is completely empty, all seats available
        if (empty(array_filter($currentSeatPlan, function ($a) {
            return $a !== null;
        }))) {
            return [0 => [
                "start" => 0,
                "seats" => $seats,
            ]];
        }

        // Table is neither empty nor full, check for empty seats in a group and return them
        $counter = 0;
        for ($i = 0; $i < $seats; $i++) {
            if (is_null($currentSeatPlan[$i])) {
                // n seats in a group empty
                $counter++;
            } else {
                if ($counter > 0) {
                    // Group of empty seats has been found
                    $emptySeats[] = [
                        "start" => ($i - $counter), // where to start in array
                        "seats" => $counter,
                    ];
                    // reset counter for next finding
                    $counter = 0;
                }
            }
            // "Last" and "first seat is empty, so add to this finding the first finding
            if (($i == ($seats - 1)) && is_null($currentSeatPlan[0]) && is_null($currentSeatPlan[$seats - 1])) {
                $emptySeats[] = [
                    "start" => ($i - $counter) + 1, // where to start in array
                    "seats" => $emptySeats[0]["seats"] + $counter,// add also seats from first finding
                ];
                unset($emptySeats[0]); // the first finding is not necessary anymore
            } else if (($i == ($seats - 1))) {
                if ($counter > 0) {
                    $emptySeats[] = [
                        "start" => ($i - $counter) + 1, // where to start in array
                        "seats" => $counter,
                    ];
                }
            }
        }

        return $emptySeats;
    }
}

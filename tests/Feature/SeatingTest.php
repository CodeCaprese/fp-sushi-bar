<?php

namespace Tests\Feature;

use App\Http\FPLib\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SeatingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // For every test there should be a fresh database
        Artisan::call("migrate:fresh");
    }

    private function initializeDatabase($seats)
    {
        Settings::groupNumber(1);
        Settings::amountOfSeats($seats);
        Settings::currentSeatPlan(array_fill(0, $seats, null));
    }

    /**
     * Test that customer can not seat at table because the group of customer is bigger than seats at table.
     *
     * @return void
     */
    public function test_seating_not_possible_group_more_then_seats()
    {
        $numberOfSeats = 10;
        // prepare
        $this->initializeDatabase($numberOfSeats);

        // test
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => ($numberOfSeats + 1),
            ]);

        // validation
        $response->assertSessionHasErrors("numberOfCustomers",
            trans("validation.custom.numberOfCustomers.lte", ["value" => $numberOfSeats])
        );
    }

    /**
     * Test that user can seat group of people at the table, when table is empty.
     * Group number is one.
     *
     * @return void
     */
    public function test_seating_possible_on_empty_table()
    {
        $expectedSeatPlan = [
            "1", "1", "1", "1", "1", "1", null, null, null, null
        ];
        $numberOfSeats = 10;
        // prepare
        $this->initializeDatabase($numberOfSeats);

        //test
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => ($numberOfSeats - 4),
            ]);
        // validation
        $response->assertSessionHas("success",
            trans("table.success_seating")
        );

        $this->assertSame(Settings::currentSeatPlan(), $expectedSeatPlan);
    }

    /**
     * Test that user can seat group of people at the table, when table is not empty.
     * Group number is two and the seating starts at a higher position.
     *
     * @return void
     */
    public function test_seating_possible_on_not_empty_table()
    {
        $expectedSeatPlan = [
            "1", "1", "1", "1", "1", "1", "2", "2", "2", null
        ];
        $numberOfSeats = 10;
        $numberOfFirstCustomer = 6;
        $numberOfCustomer = 3;

        // prepare
        $this->initializeDatabase($numberOfSeats);
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => $numberOfFirstCustomer,
            ]);

        //test
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => $numberOfCustomer,
            ]);

        // validation
        $response->assertSessionHas("success",
            trans("table.success_seating")
        );

        $this->assertSame(Settings::currentSeatPlan(), $expectedSeatPlan);
    }

    /**
     * Test that user can seat group of people at the table, when table has the exact amount of seats in a group empty.
     *
     * @return void
     */
    public function test_seating_possible_perfect_match_on_empty_group()
    {
        $prepareSeatPlan = [
            "1", "1", "1", "1", "1", "1", null, null, "3", "3"
        ];
        $expectedSeatPlan = [
            "1", "1", "1", "1", "1", "1", "4", "4", "3", "3"
        ];
        $numberOfSeats = 10;
        $numberOfCustomer = 2;
        $groupNumber = 4;

        // prepare
        $this->initializeDatabase($numberOfSeats);
        Settings::currentSeatPlan($prepareSeatPlan);
        Settings::groupNumber($groupNumber);

        //test
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => $numberOfCustomer,
            ]);

        // validation
        $response->assertSessionHas("success",
            trans("table.success_seating")
        );

        $this->assertSame(Settings::currentSeatPlan(), $expectedSeatPlan);
    }

    /**
     * Test that user can seat group of people at the table, when table is not empty.
     * Group number is four and the seating starts at a higher position and ends at
     * second entry of array. The ring is simulated and "closed".
     *
     * @return void
     */
    public function test_seating_possible_as_ring_table()
    {
        $prepareSeatPlan = [
            null, null, "2", "2", null, "3", "3", "3", null, null
        ];
        $expectedSeatPlan = [
            "4", "4", "2", "2", null, "3", "3", "3", "4", "4"
        ];
        $numberOfSeats = 10;
        $numberOfCustomer = 4;
        $groupNumber = 4;

        // prepare
        $this->initializeDatabase($numberOfSeats);
        Settings::currentSeatPlan($prepareSeatPlan);
        Settings::groupNumber($groupNumber);

        // test
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => $numberOfCustomer,
            ]);

        // validation
        $response->assertSessionHas("success",
            trans("table.success_seating")
        );

        $this->assertSame(Settings::currentSeatPlan(), $expectedSeatPlan);
    }

    /**
     * Test that user can seat group of customer at the table, when table is not empty.
     * Three customer have to be seated. There are two empty spots with four and five empty seats in a row.
     * The customer have to be at the spot with four empty seats.
     * Group number is four.
     *
     * @return void
     */
    public function test_seating_best_option()
    {
        $prepareSeatPlan = [
            null, null, "2", null, null, null, null, null, "3", "3", null, null
        ];
        $expectedSeatPlan = [
            "4", null, "2", null, null, null, null, null, "3", "3", "4", "4"
        ];
        $numberOfSeats = 12;
        $numberOfCustomer = 3;
        $groupNumber = 4;

        // prepare
        $this->initializeDatabase($numberOfSeats);
        Settings::currentSeatPlan($prepareSeatPlan);
        Settings::groupNumber($groupNumber);

        // test
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => $numberOfCustomer,
            ]);

        // validation
        $response->assertSessionHas("success",
            trans("table.success_seating")
        );

        $this->assertSame(Settings::currentSeatPlan(), $expectedSeatPlan);
    }

    /**
     * Test that user can't seat group of customer at the table, when table is not empty,
     * but the empty spots are too little for the customer group.
     * Group number is four.
     *
     * @return void
     */
    public function test_seating_not_possible_spots_not_big_enough()
    {
        $prepareSeatPlan = [
            null, null, "2", null, null, null, null, null, "3", "3", null, null
        ];
        $numberOfSeats = 12;
        $numberOfCustomer = 6;
        $groupNumber = 4;

        // prepare
        $this->initializeDatabase($numberOfSeats);
        Settings::currentSeatPlan($prepareSeatPlan);
        Settings::groupNumber($groupNumber);

        // test
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => $numberOfCustomer,
            ]);

        // validation
        $response->assertSessionHas("error",
            trans("table.error_seating")
        );

        $this->assertSame(Settings::currentSeatPlan(), $prepareSeatPlan);
    }

    /**
     * Test that user can't seat group of customer at the table, when table is full.
     * Group number is four.
     *
     * @return void
     */
    public function test_seating_not_possible_full_table()
    {
        $prepareSeatPlan = [
            "5", "5", "2", "2", "2", "2", "3", "3", "3", "3", "4", "4"
        ];
        $numberOfSeats = 12;
        $numberOfCustomer = 6;
        $groupNumber = 4;

        // prepare
        $this->initializeDatabase($numberOfSeats);
        Settings::currentSeatPlan($prepareSeatPlan);
        Settings::groupNumber($groupNumber);

        // test
        $response = $this->post(route("table.seat"),
            [
                "numberOfCustomers" => $numberOfCustomer,
            ]);

        // validation
        $response->assertSessionHas("error",
            trans("table.error_seating")
        );

        $this->assertSame(Settings::currentSeatPlan(), $prepareSeatPlan);
    }

    /**
     * Test that user can bill customer and free the seats
     * Group number is two.
     *
     * @return void
     */
    public function test_bill_customer_and_free_seats()
    {
        $prepareSeatPlan = [
            "5", "5", "2", "2", "2", "2", "3", "3", "3", "3", "4", "4"
        ];
        $expectedSeatPlan = [
            "5", "5", null, null, null, null, "3", "3", "3", "3", "4", "4"
        ];
        $numberOfSeats = 12;
        $groupToBill = 2;

        // prepare
        $this->initializeDatabase($numberOfSeats);
        Settings::currentSeatPlan($prepareSeatPlan);

        // test
        $response = $this->post(route("table.billing"),
            [
                "group" => $groupToBill,
            ]);

        // validation
        $response->assertSessionHas("success",
            trans("table.success_billing")
        );

        $this->assertSame(Settings::currentSeatPlan(), $expectedSeatPlan);
    }

    /**
     * Test that user can't bill customer and free the seats, because group number is not existing.
     *
     * @return void
     */
    public function test_bill_customer_with_wrong_group_number()
    {
        $prepareSeatPlan = $expectedSeatPlan = [
            "5", "5", "2", "2", "2", "2", "3", "3", "3", "3", "4", "4"
        ];

        $numberOfSeats = 12;
        $groupToBill = 6;

        // prepare
        $this->initializeDatabase($numberOfSeats);
        Settings::currentSeatPlan($prepareSeatPlan);

        // test
        $response = $this->post(route("table.billing"),
            [
                "group" => $groupToBill,
            ]);

        // validation
        $response->assertSessionHasErrors("group",
            trans("validation.billing_rule_group_not_at_table")
        );

        $this->assertSame(Settings::currentSeatPlan(), $expectedSeatPlan);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Http\FPLib\Settings;

class HomeController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     *
     * Show home view to set table size.
     */
    public function index()
    {
        return view("sites.home.index");
    }

    /**
     * @param SettingsRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * Store the table size and initialize needed settings.
     */
    public function store(SettingsRequest $request)
    {
        Settings::groupNumber(1);
        Settings::amountOfSeats($request["numberOfSeats"]);
        Settings::currentSeatPlan(array_fill(0, $request["numberOfSeats"], null));

        return redirect(route("table.index"))->with("success", trans("home.store_successful"));
    }
}

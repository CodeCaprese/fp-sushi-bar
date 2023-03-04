@extends("templates.template")

@section("content")
    <div class="row">
        <div class="col">
            <h3>{{__("table.header_add_new_customer")}}</h3>
            <form action="{{route("table.seat")}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-4 mb-2">
                        <input type="number" step="1" class="form-control" id="numberOfCustomers"
                               name="numberOfCustomers" min="1" value="{{old("numberOfCustomers")}}"
                               placeholder="{{__("table.label_add_new_customer")}}">
                        <small class="text-danger">{{$errors->first("numberOfCustomers")}}</small>

                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">{{__("table.button_seat_customer")}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Seat</th>
                    <th scope="col">Group</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @for($i = 0; $i < $data["numberOfSeats"]; $i++)
                    <tr
                        class="
                        @if(!is_null($data["takenSeats"][$i]))
                            bg-success text-white
                        @else
                            bg-light
                        @endif
                        "
                    >
                        <td>{{($i+1)}}</td>
                        <td>{{$data["takenSeats"][$i]}}</td>
                        <td>
                            @if(!is_null($data["takenSeats"][$i]))
                                <form action="{{route("table.billing")}}" method="POST">
                                    @csrf
                                    <input hidden name="group" value="{{$data["takenSeats"][$i]}}">
                                    <button class="btn">
                                        <i class="fa-solid fa-money-bill-1 fa-2x text-white" data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{__("table.tooltip_bill_customer", ["No" => $data["takenSeats"][$i]])}}">

                                        </i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endfor

                </tbody>
            </table>
        </div>
    </div>

@endsection()

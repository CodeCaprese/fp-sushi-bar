@extends("templates.template")

@section("content")
    <h1>{{__("home.header_home")}}</h1>
    <form action="{{route("home.store")}}" method="POST">
        @csrf
        <div class="col-4 mb-2">
            <label for="numberOfSeats" class="form-label">{{__("home.form_label_number_seats")}}</label>
            <input type="number" step="1" class="form-control" id="numberOfSeats" name="numberOfSeats" min="1"
                   aria-describedby="numberOfSeatsHelp" value="{{old("numberOfSeats")}}">
            <div id="numberOfSeatsHelp" class="form-text">{{__("home.form_help_number_seats")}}</div>
            <small class="text-danger">{{$errors->first("numberOfSeats")}}</small>
        </div>

        <button type="submit" class="btn btn-primary">{{__("home.form_button_lets_go")}}</button>
    </form>

@endsection

<!--- Start Alert messages ------------->
@if ($message = session('success'))
    <div class="alert alert-success">
        <strong>{{ $message }}</strong>
    </div>
@endif
@if ($message = session('error'))
    <div class="alert alert-danger">
        <strong>{{ $message }}</strong>
    </div>
@endif

@if ($message = session('warning'))
    <div class="alert alert-warning">
        <strong>{{ $message }}</strong>
    </div>
@endif

@if ($message = session('info'))
    <div class="alert alert-info">
        <strong>{{ $message }}</strong>
    </div>
@endif
<!--- End Alert messages ------------->

@push("scripts")
    <script>
        // --- Flash messages ------------->

        $(function() {
            // setTimeout() function will be fired after page is loaded
            // it will wait for 5 sec. and then will fire
            // $("#successMessage").hide() function
            setTimeout(function() {
                $(".alert").hide('slow')
            }, 5000);
        });
        // --- End Flash messages ------------->

    </script>
@endpush

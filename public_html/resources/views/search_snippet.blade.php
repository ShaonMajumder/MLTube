@section('search')
    <search :query="'{{ request()->query('q') ? request()->query('q'):"" }}'" ></search>
@endsection
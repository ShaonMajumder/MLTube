@section('search')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    
    <!--form action="search">
        
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="{{-- request()->query('q') ? request()->query('q'):"Search videos and channels" --}}"/>
            <div class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form-->

    <search :query="'{{ request()->query('q') ? request()->query('q'):"" }}'" ></search>
@endsection
@extends('layouts.app')

@include('search_snippet')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            


            @if($channels->count() !== 0)
                <div class="card mt-5">
                    <div class="card-header">
                        Channels
                    </div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <th>Name</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach($channels as $channel)
                                    <tr>
                                        <td>
                                            {{ $channel->name }}
                                        </td>
                                        <td>
                                            <a href="{{ route(\App\Enums\RouteEnum::CHANNELS_SHOW, $channel->id) }}" class="btn btn-sm btn-info">View Channel</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="row justify-content-center col-sm-12">
                            {{ $channels->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif

            @if($videos->count() !== 0)
                <div class="card mt-5">
                    <div class="card-header">
                        Videos
                    </div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <th>Name</th>
                                <th></th>
                            </thead>
                            <tbody>
                                @foreach($videos as $video)
                                    <tr>
                                        <td>
                                            {{ $video->title }}
                                        </td>
                                        <td>
                                            <a href="{{ route('videos.show', $video->id) }}" class="btn btn-sm btn-info">View Video</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="row justify-content-center col-sm-12">
                            {{ $videos->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
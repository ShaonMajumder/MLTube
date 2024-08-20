@extends('layouts.app')
@include('search_snippet')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-7 col-sm-6 col-xs-12">
            @include('videoplayer')
            <comments :video="{{ $video }}"></comments>

        </div>
        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
            <div class="row justify-content-center">
                <div class="card-group col-sm-12 row row-cols-1 row-cols-lg-1">
                    @if($related_videos)
                        @foreach ($related_videos as $video)
                            <div class="col mb-4">
                                <a href="{{ route('videos.show', $video->id) }}" class="text-decoration-none text-dark">
                                    <div class="card h-100 shadow-sm">
                                        <img class="card-img-top img-fluid" src="{{ $video->thumbnail }}" alt="{{ $video->title }}" style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $video->title }}</h5>
                                            <p class="card-text text-truncate">{{ $video->description }}</p>
                                            <p class="card-text"><small class="text-muted">Last updated {{ $video->updated_at->diffForHumans() }}</small></p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center">No related videos available.</p>
                    @endif
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('styles')
    <link href="https://vjs.zencdn.net/7.10.2/video-js.css" rel="stylesheet" />
    <!--7.4.1-->

    <style>
        .vjs-default-skin {
            width: 100%;
        }
        .thumbs-up, .thumbs-down {
            width: 20px;
            height: 20px;
            cursor: pointer;
            fill: currentColor;
        }

        .thumbs-down-active, .thumbs-up-active {
            color: #3EA6FF;
        }

        .thumbs-down {
            margin-left: 1rem;
        }
    </style>

    <style>
        .w-full {
            width: 100% !important;
        }
        .w-80 {
            width: 80% !important;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://vjs.zencdn.net/7.10.2/video.min.js"></script>
    <!-- 7.5.4 and video.js -->

    <script>
        window.CURRENT_VIDEO = '{{ $video->id }}'
    </script>
    <script src='{{ asset('js/player.js') }}'></script>
@endsection

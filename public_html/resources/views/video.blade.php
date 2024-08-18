@extends('layouts.app')
@include('search_snippet')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-7 col-sm-6 col-xs-12">
            <div class="card">
                @if($video->editable())
                    <form action="{{ route('videos.update', $video->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                @endif

                    <div class="card-header">{{ $video->title }}</div>

                    <div class="card-body">
                        <video-js id="video" class="vjs-default-skin" controls preload="auto" width="640" height="268">
                            <source src='{{ asset(Storage::url("videos/{$video->id}/{$video->id}.m3u8")) }}' type="application/x-mpegURL">
                        </video-js>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mt-3">
                                    @if($video->editable())
                                        <input type="text" class="form-control" value="{{ $video->title }}" name="title">
                                    @else
                                        {{ $video->title }}
                                    @endif
                                </h4>
                                {{ $video->views }} {{ Str::plural('view', $video->views) }}
                            </div>
                            <votes :default_votes='{{ $video->votes }}' entity_id="{{ $video->id }}" entity_type="video" entity_owner="{{ $video->channel->user_id }}"> </votes>
                        </div>

                        <hr>

                        <div>
                            {{ 'Tags : '.$tags ?? ''}}
                            
                            @if($video->editable())
                                <textarea name="description" cols="3" rows="3" class="form-control">{{ $video->description }}</textarea>

                                <div class="text-right mt-4">
                                        <button class="btn btn-info btn-sm" type="submit">Update video details</button>
                                </div>
                            @else
                                {{ $video->description }}
                            @endif
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <div class="media">
                                <img class="rounded-circle" src="https://picsum.photos/id/42/200/200" width="50" height="50" class="mr-3" alt="...">
                                <div class="media-body ml-2">
                                    <h5 class="mt-0 mb-0">
                                        {{ $video->channel->name }}
                                    </h5>
                                    <span class="small">Published on {{ $video->created_at->toFormattedDateString() }}</span>
                                </div>
                            </div>

                            <subscribe-button :channel="{{ $video->channel }}" :initial-subscriptions="{{ $video->channel->subscriptions }}" />
                        </div>
                    </div>
                @if($video->editable())
                </form>
                @endif
            </div>

            

        </div>
        <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
            
            

            <?php $videos = App\Models\Video::simplePaginate(4); ?>
            <div class="row justify-content-center">
                <div class="card-group col-sm-12 row row-cols-1 row-cols-lg-1">

                    @foreach ($videos as $video)
                        <div class="col mb-4">
                            <a href="{{ route('videos.show', $video->id) }}">
                                <div class="card">
                              
                                    <img class="card-img-top  img-fluid" style="width: 18rem; height: 20rem;" src="{{$video->thumbnail}}"  alt="...">
                                  
                                    <!-- 'thumbnails/vid_thumb.png' -->
                                    <div class="card-body">
                                    <h5 class="card-title">{{ $video->title }}</h5>
                                    <p class="card-text" class="truncate-overflow">{{ $video->description }}</p>
                                    <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                                    </div>
                                </div>
                            </a>
                            
                        </div>
                    @endforeach        
                    
                </div>

                <!-- /*$videos->links()*/ -->
                
            </div>



        </div>
        <div class="col-lg-12">
            <comments :video="{{ $video }}"></comments>
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

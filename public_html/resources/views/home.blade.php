@extends('layouts.app')

@include('search_snippet')

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="row justify-content-center">
            <div class="card-group col-xs-12 col-sm-12 col-md-12 col-lg-12 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-3">

                @foreach ($videos as $video)
                    <?php $video_channel_link = App\Models\Channel::where('id', '=', "$video->channel_id" )->first(); ?>
                    <div class="col mb-3" >
                        <a href="{{ route('videos.show', $video->id) }}" style=" color: inherit; text-decoration:none;">
                            <div class="card video_card">
                        
                                <img class="card-img-top video_card_thumbnail" 
                                    src="{{ asset('images/loading.gif') }}"
                                    data-src="{{ route('thumbnails.show', ['filename' => basename( parse_url($video->thumbnail)['path'] ) ?? 'failed', 'w' => 150, 'h' => 100]) }}" 
                                    alt="{{ $video->title }}">

                                <!-- 'thumbnails/vid_thumb.png' -->
                                <div class="card-body">
                                    <div class="row "> 
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"> 
                                            <div class="video_card_channel_avatar">
                                                <avatar-user :image="'{{ $video_channel_link->image() }}'" :href="'/channels/{{$video_channel_link->id}}'" :username="'{{ addslashes($video_channel_link->name) }}'" :size=35 :rounded=true ></avatar-user>
                                            </div>
                                            
                                        </div>
                                        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                                            <h5 class="card-title video_card_title" >{{ $video->title }}</h5>
                                            {{-- <p class="card-text video_card_description">{{ $video->description }}</p> --}}
                                            <small class="text-muted">
                                                {{ $video->channel->name }}
                                                {{-- @if ($video->channel->verified) --}}
                                                    <i class="fas fa-check-circle text-primary" title="Verified"></i>
                                                {{-- @endif --}}
                                            </small>
                                            <p class="card-text"><small class="text-muted">{{ $video->views }} views . {{FirstRelativeTime($video->created_at)}}</small></p>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </a>
                        
                    </div>
                @endforeach        
                
            </div>

            {{ $videos->links() }}
            
        </div>
    </div>
@endsection

@section('footer-script')
<script>
    // Function to load image asynchronously
    function loadImageAsync(imgElement) {
        const actualSrc = imgElement.getAttribute('data-src');

        // Create a new image object to preload
        const img = new Image();
        img.src = actualSrc;

        img.onload = function() {
            // Once the image is loaded, replace the placeholder
            imgElement.src = actualSrc;
        };
    }

    // Get all images with the 'data-src' attribute and load them asynchronously
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(function(img) {
            loadImageAsync(img);
        });
    });
</script>
@endsection
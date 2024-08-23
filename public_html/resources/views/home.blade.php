@extends('layouts.app')

@include('search_snippet')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-12">
            
            <!--
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="">
                        <input type="text" name="search" class="form-control" placeholder="Search videos and channels">
                    </form>
                </div>
            </div>
            -->
            
            <?php $videos = App\Models\Video::Paginate(4); ?>
            <div class="row justify-content-center">
                <div class="card-group col-xs-12 col-sm-12 col-md-12 col-lg-12 row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">

                    @foreach ($videos as $video)
                        <?php $video_channel_link = App\Models\Channel::where('id', '=', "$video->channel_id" )->first(); ?>
                        <div class="col mb-3" >
                            <a href="{{ route('videos.show', $video->id) }}" style=" color: inherit; text-decoration:none;">
                                <div class="card video_card">
                              
                                    <img class="card-img-top video_card_thumbnail" src="{{$video->thumbnail}}?w=150&h=100"  alt="...">
                                  
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
                                        </div>
                                    </div>
                                    <p class="card-text video_card_description">{{ $video->description }}</p>
                                    <p class="card-text"><small class="text-muted">Last updated {{FirstRelativeTime($video->created_at)}}</small></p>
                                    </div>
                                </div>
                            </a>
                            
                        </div>
                    @endforeach        
                    
                </div>

                {{ $videos->links() }}
                
            </div>




        </div>
    </div>
</div>

@endsection
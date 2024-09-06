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
                    <div>
                        {{ $video->description }}
                    </div>
                @endif
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center mt-5">
                <div class="media">
                    <img class="rounded-circle" src="https://picsum.photos/id/42/200/200" width="50" height="50" class="mr-3" alt="...">
                    <div class="media-body ml-2">
                        <h5 class="mt-0 mb-0">
                            <a :href="`{{ route( \App\Enums\RouteEnum::CHANNELS_SHOW, [ 'channel' => $video->channel->id ] ) }}`"  class="channel-link">
                                {{ $video->channel->name }}
                            </a>
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
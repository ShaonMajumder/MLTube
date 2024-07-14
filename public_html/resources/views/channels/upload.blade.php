@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <channel-uploads :channel="{{ $channel }}" inline-template>
            <div class="col-md-8">
                
                <div class="card p-3 d-flex justify-content-center align-items-center" v-if="!selected">
                    <!--svg onclick="document.getElementById('video-files').click()" width="70px" height="70px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 461 461"><path d="M365.26 67.4H95.74A95.74 95.74 0 0 0 0 163.13v134.73a95.74 95.74 0 0 0 95.74 95.74h269.52A95.74 95.74 0 0 0 461 297.87V163.14a95.74 95.74 0 0 0-95.74-95.75zM300.5 237.05l-126.06 60.12a5.06 5.06 0 0 1-7.24-4.57v-124a5.06 5.06 0 0 1 7.34-4.52l126.06 63.88a5.06 5.06 0 0 1-.1 9.09z" fill="#f61c0d"/></svg-->
                    <svg onclick="document.getElementById('video-files').click()" width="70px" height="70px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 111.34"><path d="M23.59,0h75.7a23.68,23.68,0,0,1,23.59,23.59V87.75A23.56,23.56,0,0,1,116,104.41l-.22.2a23.53,23.53,0,0,1-16.44,6.73H23.59a23.53,23.53,0,0,1-16.66-6.93l-.2-.22A23.46,23.46,0,0,1,0,87.75V23.59A23.66,23.66,0,0,1,23.59,0ZM54,47.73,79.25,65.36a3.79,3.79,0,0,1,.14,6.3L54.22,89.05a3.75,3.75,0,0,1-2.4.87A3.79,3.79,0,0,1,48,86.13V50.82h0A3.77,3.77,0,0,1,54,47.73ZM7.35,26.47h14L30.41,7.35H23.59A16.29,16.29,0,0,0,7.35,23.59v2.88ZM37.05,7.35,28,26.47H53.36L62.43,7.38v0Zm32,0L59.92,26.47h24.7L93.7,7.35Zm31.32,0L91.26,26.47h24.27V23.59a16.32,16.32,0,0,0-15.2-16.21Zm15.2,26.68H7.35V87.75A16.21,16.21,0,0,0,12,99.05l.17.16A16.19,16.19,0,0,0,23.59,104h75.7a16.21,16.21,0,0,0,11.3-4.6l.16-.18a16.17,16.17,0,0,0,4.78-11.46V34.06Z"/></svg>
                    <input multiple type="file" id="video-files" ref="videos" @change="upload" hidden>
                        <p class="text center">Upload Videos</p>
                </div>
                
                <div class="card p-3" v-else>
                    <div class="my-4" v-for="video in videos">
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated " role="progressbar" :style="{ width: `${video.percentage || progress[video.name]}%` }" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                @{{ video.percentage ? video.percentage === 100 ? 'Video Processing completed.' : 'Processing' : 'Uploading' }}
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-md-4">
                                <div v-if="!video.thumbnail" class="d-flex justify-content-center align-items-center" style="height: 180px; color: white; font-size: 18px; background: #808080;">
                                        Loading thumbnail ...
                                </div>
                                <img v-else :src="video.thumbnail" style="width: 100%;" alt="">
                            </div>
        
                            <div class="col-md-4">
                                <a v-if="video.percentage && video.percentage === 100" target="_blank" :href="`/videos/${video.id}`">
                                    @{{ video.title }}
                                </a>
                                <h4 v-else class="text-center">
                                    @{{ video.title || video.name }}
                                </h4>
                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </channel-uploads>
    </div>
</div>
@endsection

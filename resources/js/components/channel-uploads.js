//const { constant } = require("lodash")

//const { default: Axios } = require("axios")

Vue.component('channel-uploads', {
    props: {
        channel: {
            type: Object,
            required: true,
            default: () => ({})
        }
    },
    data: () => ({
        selected: false,
        videos: [],
        progress: {},
        uploads: [],
        intervals: {}
    }),
    methods: {
        upload(){
            
            console.log(this.$refs)
            this.selected = true
            this.videos = Array.from(this.$refs.videos.files)
            //console.log(videos)
            const uploaders = this.videos.map(video => {
                const form = new FormData()
                this.progress[video.name] = 0
                form.append('video',video)
                form.append('title',video.name)
                return axios.post(`/channels/${this.channel.id}/videos`, form, {
                    onUploadProgress: (event) => {
                        this.progress[video.name] = Math.ceil((event.loaded / event.total) * 100)
                        this.$forceUpdate()
                    }

                }).then(({ data }) => {
                    this.uploads = [
                        ...this.uploads,
                        data 
                    ]
                })
            })

            axios.all(uploaders)
                .then(() => {
                    this.videos = this.uploads
                    this.videos.forEach(video => {
                        this.intervals[video.id] = setInterval(()=> {
                            axios.get(`/videos/${video.id}`).then(({ data }) => {
                                if(data.percentage === 100){
                                    clearInterval(this.intervals[video.id])
                                }
                                this.videos = this.videos.map(v => {
                                    if( v.id === data.id){
                                        
                                        /*
                                        axios.post(`/videos/${v.id}/object_tags`, {
                                            action: 'ml_tags',
                                            vid_id: v.id
                                        }).then((response) => {
                                            console.log(response);
                                        }, (error) => {
                                            console.log(error);
                                        });
                                        */

                                        return data
                                    }
                                    return v
                                })
                            })
                        }, 3000)
                    })
                })
            
        }
    }
})

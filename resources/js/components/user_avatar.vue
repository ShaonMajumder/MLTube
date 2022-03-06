<template>
  <a :href=localHref style="text-decoration: none;">
    <img v-if="localImage" :src=localImage class="rounded-circle" alt="" style="width:100%">
    <Avatar v-if="!localImage" :username="localUsername" :rounded=localRounded :size=localSize ></Avatar>
  </a>
</template>


<script>
import Avatar from 'vue-avatar'
export default {
  name: "avatar",
  props: {
    username: String,
    size: Number,
    rounded: Boolean,
    href: String,
    image: String
  },
  components: {
    Avatar
  },
  data() {
    return {
      localValue: ""
    };
  },
  created() {
    this.localUsername = this.username;
    this.localSize = this.size;
    this.localRounded = this.rounded;
    this.localHref = this.href;
    this.localImage = this.image;
  }
};
</script>
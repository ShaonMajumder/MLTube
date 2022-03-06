<template>
    <Avatar :username="localUsername" :rounded=localRounded :size=localSize ></Avatar>
</template>


<script>
import Avatar from 'vue-avatar'
export default {
  name: "avatar",
  props: {
    username: String,
    size: Number,
    rounded: Boolean
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
  }
};
</script>
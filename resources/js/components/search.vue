<style >
/*search bar */
.box {
  width: 80%;
  height: 58px;
  background-color: #fff;
  margin: 20px auto;
  box-shadow: 0 2px 33px rgba(0,0,0,.77)
}

.search-bar {
  width: 55%;
  margin: 0px auto;
  line-height: 56px;
  position: relative
}

.search-bar input[type="text"] {
  width: 89%;
  height: 33px;
  padding: 0 10px;
  color: #000;
  border: 1px solid #BEBEBE;
  border-radius: 2px 0 0 2px;
  z-index: 2
}

.search-bar input:focus {
  border: 1.1px solid #1c62b9;
  box-shadow: inset 0 1px 2px rgba(0,0,0,.4);
}

.search-bar button {
  position: absolute;
  width: 11%;
  height: 33px;
  border: 1px solid #BEBEBE;
  border-left: 0;
  top: 13px;
  right: 0;
  border-radius: 0px 2px 2px 0;
  cursor: pointer;
  font-size: 14px;
  text-align: center;
  background-color: #f0f0f0;
  z-index: 1
}

.search-bar button i {
  opacity: .5;
  position: absolute;
  top: 25%;
  right: 25%;
  transition: all .07s ease-in-out
}

.search-bar button:hover i {
  opacity: .95
}

.search-bar button:hover {
  background-color: #E4E4E4;
  box-shadow: 0 1px 1px rgba(0,0,0,.15),
    inset 0 0 2px rgba(0,0,0,.05);
  transition: all .07s ease-in-out
}

/* ==-====-===-==== */

.search-bar h2 {
  color: #fff;
  text-transform: uppercase;
  text-align: center;
  font-weight: bolder;
  font-size: 30px;
  text-shadow: 0 0 20px black;
  cursor: none;
  margin-top: 22px
}
</style>

<template>

  <div class="search-bar">
    <form action="/search">
      <input type="text" name="q"  placeholder="Search" v-model="localValue" />
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
  </div>
</template>





<script>
export default {
  name: "search",
  props: {
    query: String // 1. Receives the value as a prop
  },
  data() {
    return {
      localValue: ""
    };
  },
  created() {
    this.localValue = this.query; // 2. Copy the value in a local value variable

    // Set a watcher to emit the changes to the parent component
    this.$watch("localValue", value => {
      this.$emit("input", value);
    });
  }
};
</script>
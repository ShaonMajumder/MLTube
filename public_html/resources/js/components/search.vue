<template>
  <div class="search-bar-container">
    <div class="search-bar">
      <form action="/search">
        <input type="text" name="q" placeholder="Search..." v-model="localValue" />
        <button type="submit"><i class="fas fa-search"></i></button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "search",
  props: {
    query: String // Receives the value as a prop
  },
  data() {
    return {
      localValue: this.query || "" // Initialize with the prop value or empty
    };
  },
  watch: {
    localValue(value) {
      this.$emit("input", value); // Emit the changes to the parent component
    }
  }
};
</script>

<style scoped>
.search-bar-container {
  max-width: 600px; /* Set a maximum width */
  margin: 20px 0; /* Margin to space out from other elements */
  padding: 0 15px; /* Padding to keep the search bar inside the container */
}

.search-bar {
  display: flex; /* Flexbox to align the input and button */
  border-radius: 8px; /* Rounded corners */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
  overflow: hidden; /* Hide overflow to keep rounded corners */
}

.search-bar form {
  display: flex; /* Ensure input and button are aligned horizontally */
  width: 100%; /* Full width of the parent container */
  margin: 0; /* Remove default margin */
}

.search-bar input[type="text"] {
  flex: 1; /* Take up remaining space */
  height: 40px; /* Increased height */
  padding: 0 15px; /* Adequate padding */
  color: #333; /* Dark text color */
  border: none; /* Remove internal border */
  border-radius: 8px 0 0 8px; /* Rounded corners on the left */
  font-size: 16px; /* Font size */
  box-shadow: inset 0 0 0 1px #ced4da; /* Internal border */
  transition: border-color 0.2s ease, box-shadow 0.2s ease; /* Smooth transitions */
}

.search-bar input:focus {
  outline: none; /* Remove default outline */
  border-color: #007bff; /* Change border color on focus */
  box-shadow: inset 0 0 0 2px #007bff; /* Highlight input on focus */
}

.search-bar button {
  width: 60px; /* Fixed width */
  height: 40px; /* Match input height */
  border: none; /* Remove internal border */
  border-radius: 0 8px 8px 0; /* Rounded corners on the right */
  background-color: #007bff; /* Primary color background */
  color: #fff; /* White text color */
  cursor: pointer;
  font-size: 16px; /* Font size */
  transition: background-color 0.2s ease, box-shadow 0.2s ease; /* Smooth transitions */
}

body.dark-theme .search-bar button {
  background-color: #302E32;
}
/* Button hover state */
.search-bar button:hover {
  background-color: #0056b3; /* Darker blue on hover */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Soft shadow effect */
}

/* Icon styling inside the button */
.search-bar button i {
  font-size: 18px; /* Icon size */
  opacity: 0.8; /* Slightly less transparent */
  transition: opacity 0.2s ease; /* Smooth transition for opacity */
}

/* Icon hover state */
.search-bar button:hover i {
  opacity: 1; /* Fully opaque on hover */
}
</style>

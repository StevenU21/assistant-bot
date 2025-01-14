<template>
  <div class="relative ms-3" ref="dropdown">
    <span
      @click="toggleDropdown"
      :class="{
        'inline-flex items-center rounded-md px-3 py-2 text-sm font-medium cursor-pointer': true,
        'bg-red-100 text-red-800': requestCount === 0,
        'bg-gray-700 text-white': requestCount > 0
      }"
    >
      Requests left: {{ requestCount }}
    </span>
    <div v-if="dropdownOpen" class="absolute mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
      <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
        <button @click="resetCount" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
          </svg>
          Reestablecer
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RequestCount',
  data() {
    return {
      dropdownOpen: false
    };
  },
  computed: {
    requestCount() {
      return this.$page.props.auth.user.user_request.request_count;
    }
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    resetCount() {
      // Lógica para reestablecer el conteo
      console.log('Reestablecer conteo');
    },
    handleClickOutside(event) {
      if (this.$refs.dropdown && !this.$refs.dropdown.contains(event.target)) {
        this.dropdownOpen = false;
      }
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleClickOutside);
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>

<template>
  <div class="relative ms-3">
    <span
      :class="{
        'inline-flex items-center rounded-md px-3 py-2 text-sm font-medium': true,
        'bg-red-100 text-red-800': requestCount === 0,
        'bg-gray-700 text-white': requestCount > 0
      }"
    >
      Requests left: {{ requestCount }}
    </span>
  </div>
</template>

<script>
export default {
  name: 'RequestCount',
  computed: {
    requestCount() {
      return this.$page.props.auth.user.user_request.request_count;
    }
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>

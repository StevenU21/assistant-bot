<template>
  <div class="relative ms-3">
    <span class="inline-flex items-center rounded-md bg-blue-100 px-3 py-2 text-sm font-medium text-blue-800">
      Requests left: {{ $page.props.auth.user.user_request.request_count }}
    </span>
  </div>
</template>

<script>
export default {
  name: 'RequestCount',
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style>

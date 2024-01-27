<template>
  <a class="invisible_link" :href="url">
    <i class="far fa-user"></i>
    <span>{{ username }}</span>
  </a>
</template>

<script>
export default {
  name: "user-badge",
  props: {
    username: String,
    url: String,
    isLocked: Boolean
  }
}
</script>

<style scoped>

</style>
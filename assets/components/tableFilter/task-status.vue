<script>


export default {
  name: "task-status",
  props: {
    // initQuery: URLSearchParams,
    items: String
  },
  data() {
    return {
      query: URLSearchParams
    }
  },
  mounted() {
    console.log(this.items);
  }
}
</script>

<template>
  <label class="form-check" v-for="item in items">
    <input class="form-check-input" type="checkbox" :name="item.name" :value="item.value" :checked="item.checked">
    <span class="form-check-label">{{ item.label }}</span>
  </label>
</template>

<style scoped lang="scss">

</style>
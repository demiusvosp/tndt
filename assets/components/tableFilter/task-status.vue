<script>

export default {
  name: "task-status",
  props: {
    label: String,
    name: String,
    options: Array,
  },
  data() {
    return {
      checked: []
    }
  },
  mounted() {
    // this.checked = this.options.map(function (item) {
    //   if (item.checked) {
    //     return item.value;
    //   }
    // });
  }
}
</script>

<template>
  <div class="filter-item col-sm-4 col-md-2">
    <div class="card">
      <div class="card-header">
        {{ label }}
      </div>
      <div class="card-body">
        <label class="form-check" v-for="option in options">
          <input class="form-check-input" type="checkbox" :value="option.value" v-model="checked">
          <span class="form-check-label">{{ option.label }}</span>
        </label>
      </div>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
<script>
import taskStatus from "./task-status.vue";
export default {
  name: "table-filter-widget",
  props: {
    submitLabel: String,
    filterData: String
  },
  components: {
    taskStatus
  },
  data: function () {
    return {
      filters: JSON.parse(this.filterData),
    };
  }
}
</script>

<template>
  <div class="row" v-for="filter in filters">
    <task-status v-bind="filter"></task-status>
  </div>
  <div class="row">
    <div class="">
      <button class="btn btn-success btn-sm" type="submit">{{ submitLabel }}</button>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
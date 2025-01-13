<script>
import taskStatus from "./task-status.vue";
export default {
  name: "table-filter-widget",
  props: {
    submitLabel: String,
    filterData: String,
  },
  components: {
    taskStatus
  },
  data: function () {
    return {
      filters: JSON.parse(this.filterData),
      query: (new URL(window.location.href)).searchParams,
      url: new URL(window.location.href),
    };
  },
  methods: {
    onFilterChange(newVal) {
      let query = this.url.searchParams;
      if (newVal.multiple) {
        let paramName = newVal.name+'[]';
        query.delete(paramName);
        newVal.value.forEach((value) => query.append(paramName, value));
      } else {
        query.set(newVal.name, newVal.value);
      }
      query.set('page', 1);
    },
    applyFilter() {
      let url = new URL(window.location.href);
      window.location.href = this.url;
    }
  }
}
</script>

<template>
  <div class="row" v-for="filter in filters">
    <task-status v-bind="filter" @change="onFilterChange"></task-status>
  </div>
  <div class="row">
    <div class="">
      <button class="btn btn-success btn-sm" type="button" @click="applyFilter">{{ submitLabel }}</button>
      <span>{{ query.toString() }}</span>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
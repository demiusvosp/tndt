<script>
import taskStatus from "./task-status.vue";
export default {
  name: "table-filter-widget",
  props: {
    filterData: String,
    submitLabel: String,
    resetLabel: String,
  },
  components: {
    taskStatus
  },
  data: function () {
    return {
      filters: JSON.parse(this.filterData),
      url: new URL(window.location.href),
    };
  },
  methods: {
    onFilterChange(newVal) {
      let query = this.url.searchParams;
      if (newVal.multiple) {
        for (let param of this.url.searchParams.keys()) {
          if (param.startsWith(newVal.name)) {
            this.url.searchParams.delete(param);
          }
        }
        newVal.value.forEach((value) => query.append(newVal.name+'[]', value));
      } else {
        query.set(newVal.name, newVal.value);
      }
    },
    applyFilters() {
      this.url.searchParams.set('page', '1');
      window.location.href = this.url;
    },
    resetFilters() {
      for (let filter in this.$refs) {
        this.$refs[filter].reset();
      }
    },
  }
}
</script>

<template>
  <div class="row">
    <task-status v-bind="filters.status" @change="onFilterChange" ref="status"></task-status>
  </div>
  <div class="row">
    <div>
      <button class="btn btn-success btn-sm me-2" type="button" @click="applyFilters">{{ submitLabel }}</button>
<!--      пока совершенно не ясно, как дать фильтру команду на сброс, учитывая что фильтры хранят значения в разных структурах-->
      <button class="btn btn-primary btn-sm me-2" type="button" @click="resetFilters">{{ resetLabel }}</button>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
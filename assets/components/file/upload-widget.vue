<script setup>
import { onMounted, useAttrs } from "vue";
const props = defineProps({
  action: String,
  project: String
});

function onSubmit(event) {
  console.log(event);
  console.log(props);
}
</script>

<template>
  <div class="card" v-bind="$attrs">
    <form autocomplete="off" novalidate @submit.prevent="onSubmit">
    <div class="card-header">
      Загрузить
    </div>
    <div class="card-body">
        <div class="fallback">
          <input name="file" type="file" />
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success">Загрузить</button>
    </div>
  </form>
  </div>
</template>

<style scoped lang="scss">

</style>
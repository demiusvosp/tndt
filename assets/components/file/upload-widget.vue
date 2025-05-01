<script setup>
import axios from 'axios';
import { ref, useTemplateRef } from 'vue';

const State = {toselect: 'toselect', selected: "selected", uploaded:'uploaded', attached:"attached"};
const fileInput = useTemplateRef('fileInput');
const state = ref(State.toselect);

const props = defineProps({
  action: String,
  target: String,
  project: String
});

function onSelect(event)
{
  state.value = State.selected;

console.log(event);
console.log(fileInput);
console.log(fileInput.value.files);
}

function onSubmit(event) {
    let formData = new FormData();
    formData.append('file', fileInput.value.files[0]);
    formData.append('target', props.target);
    formData.append('project', props.project);

    axios.post(
        props.action,
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }
    )
    .then(response => {
        console.log(response);
    })
    .catch(error => {
        console.error(error);
    })
}
</script>

<template>
  <div class="card" v-bind="$attrs">
    <form autocomplete="off" novalidate @submit.prevent="onSubmit" >
    <div class="card-header">
      Загрузить
    </div>
    <div class="card-body">
        <div class="fallback">
          <input name="file" type="file" ref="fileInput" @change="onSelect"/>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success" :disabled="state !== State.selected">Загрузить</button>
    </div>
  </form>
  </div>
</template>

<style scoped lang="scss">

</style>
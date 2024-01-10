<template>
<h1>
  {{ subjectType}} - {{ subjectId }} from vue component
</h1>
</template>

<script>
export default {
  name: "timeline-widget",
  props: {
    subjectType: String,
    subjectId: String
  },
  mounted() {
    console.log('subjectType:' + this.subjectType);
    console.log('subjectId:' + this.subjectId);
  }
}
</script>

<style scoped>

</style>
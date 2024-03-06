<template>
  <div>
    <div class="activity-head">
      <span v-html="created"></span>
      <user-badge class="ms-1" v-bind="actor"></user-badge>
    </div>
    <span class="activity-action text-secondary">
      <span v-html="type.label"></span>
    </span>
  </div>
</template>

<script>
import userBadge from "../common/user-badge";

export default {
  name: "timeline-item",
  props: {
    id: String,
    type: Object,
    created: String,
    actor: Object
  },
  components: {
    userBadge
  }
}
</script>

<style scoped lang="scss">

</style>
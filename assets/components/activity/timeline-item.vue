<template>
  <div class="timeline-item">
    <div class="head">
      <span class="created">{{ created.toLocaleString("ru", {}) }}</span>,
      <user-badge v-bind="actor"></user-badge>
      <span class="action">{{ type.label }}</span>
    </div>
    <!-- здесь мы раскроем дополнительную инфу, что имено сделал -->
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
  },
  computed: {
  }
}
</script>

<style scoped lang="scss">
@import "~bootstrap-sass/assets/stylesheets/bootstrap/variables";
.timeline-item {
  margin-bottom: 1em;

  .head {
    padding: 0 5em 2px 1em;
    border-bottom: $gray-lighter 1px solid;

    .action {
      font-weight: bold;
      margin-left: 1em;
    }
  }
}
</style>
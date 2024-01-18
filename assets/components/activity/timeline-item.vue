<template>
  <div class="timeline-item row">
    <div class="head col-lg-6 col-md-8 col-sm-12">
      <span class="created">{{ createdAt }},</span>
      <user-badge v-bind="actor"></user-badge>
      <span class="action">{{ type.label }}</span>
    </div>
    <!-- здесь мы раскроем дополнительную инфу, что имено сделал -->
  </div>
</template>

<script>
import moment from "moment";
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
    createdAt: function () {
      return moment(this.created).fromNow();
    }
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

    .created {
      margin-right: 0.3em;
    }
    .action {
      font-weight: bold;
      margin-left: 0.3em;
    }
  }
}
</style>
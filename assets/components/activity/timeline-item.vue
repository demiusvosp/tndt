<template>
  <div class="timeline-item ">
    <div class="head">
      <span class="created">{{ createdAt }},</span>
      <user-badge v-bind="actor"></user-badge>
      <span class="action">
        <span v-html="type.label"></span>
      </span>
    </div>
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
    padding: 0 1em 2px 1em;
    border-bottom: $gray-lighter 1px solid;

    .created {
      margin-right: 0.3em;
    }
    .action {
      margin-left: 0.3em;
    }
  }
}
</style>
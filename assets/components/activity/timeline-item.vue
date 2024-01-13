<template>
  <div class="timeline-item">
    <div class="head">
      <span class="created">{{ created.toLocaleString("ru", {}) }}</span>,
      <a class="invisible_link" v-bind:href="'/user/' + actor "><i class="far fa-user"></i>{{ actor }} </a>
      <span class="action">{{ type }}</span>
    </div>
    <!-- здесь мы раскроем дополнительную инфу, что имено сделал -->
  </div>
</template>

<script>
export default {
  name: "timeline-item",
  props: {
    id: String,
    type: String,
    createdAt: String,
    actor: String
  },
  computed: {
    created: function () {
      return new Date(this.createdAt);
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

    .action {
      font-weight: bold;
      margin-left: 1em;
    }
  }
}
</style>
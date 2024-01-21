<template>
<div class="activity-timeline">
  <ul class="list-unstyled">
    <li v-for="item in items">
      <timeline-item v-bind="item" v-bind:key="item.id"></timeline-item>
    </li>
    <li v-if="loaded">
      <div class="row"><div class="col-md-offset-2">
        <i class="loading fa fa-spinner fa-spin"></i>
      </div></div>
    </li>
    <li v-if="errored">
      <div class="row"><div class="col-md-4 col-sm-6">
        <div class="alert alert-danger">
          <h4><i class="icon fa fa-exclamation-triangle"></i> {{ errored }}</h4>
        </div>
      </div></div>
    </li>
  </ul>
</div>
</template>

<script>
import timelineItem from "./timeline-item";
import axios from 'axios';

export default {
  name: "timeline-widget",
  components: {
    timelineItem
  },
  props: {
    action: String
  },
  data: function () {
    return {
      items: null,
      loaded: true,
      errored: false,
      hasMore: false
    }
  },
  mounted: function() {
    axios.get(this.action)
      .then(response => {
        this.items = response.data.items;
        this.hasMore = response.data.hasMore;
        this.loaded = false;
      })
      .catch(error => {
        console.log(error.response);
        this.loaded = false;
        this.errored = error.response.data.message;
      })
  }
}
</script>

<style scoped lang="scss">
@import "~bootstrap-sass/assets/stylesheets/bootstrap/variables";
  ul {
    margin-left: 1em;
  }
  .loading {
    font-size: $font-size-h3;
  }
</style>
<template>
<div class="activity-timeline">
  <ul class="steps steps-vertical">
    <li class="step-item" v-for="item in items">
      <timeline-item v-bind="item" v-bind:key="item.id"></timeline-item>
    </li>
    <li v-if="loaded">
      <div class="row"><div class="col-md-offset-2">
        <i class="loading fa fa-spinner fa-spin"></i>
      </div></div>
    </li>
    <li v-if="empty">
      <div class="row"><div class="col-md-offset-2">
        <i class="empty">{{ empty }}</i>
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
      items: [],
      loaded: true,
      empty: false,
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
        this.empty = this.items.length === 0 ? (response.data?.emptyMessage ? response.data.emptyMessage : 'Не найден') : false;
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
@import "~bootstrap/scss/bootstrap";

.loading {
  font-size: $h3-font-size;
}
</style>
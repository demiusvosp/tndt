<template>
<div class="activity-timeline">
  {{ action }} from vue component<br/>
  <ul class="list-unstyled">
    <li v-for="item in items">
      <timeline-item v-bind="item"></timeline-item>
    </li>
  </ul>
</div>
</template>

<script>
import timelineItem from "./timeline-item";
import axios from 'axios';

export default {
  name: "timeline-widget",
  props: {
    action: String
  },
  components: {
    timelineItem
  },
  mounted: function() {
    axios.get(this.action)
      .then(response => {
        this.items = response.data.items;
        this.hasMore = response.data.hasMore;
      })
      .catch(error => {
        console.log(error);
      })
    console.log('action:' + this.action);
    console.log(this.items);
  },
  data: function () {
    return {
      items: null,
      hasMore: false
    }
  }
}
</script>

<style scoped>

</style>
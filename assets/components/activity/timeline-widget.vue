<template>
<div class="activity-timeline">
  <ul class="steps steps-vertical">
    <template v-if="curStatus === Status.success">
      <li class="step-item" v-for="item in items">
        <timeline-item v-bind="item" v-bind:key="item.id"></timeline-item>
      </li>
    </template>
    <li v-else-if="curStatus === Status.loading">
      <div class="row"><div class="col-md-offset-2">
        <i class="loading fa fa-spinner fa-spin"></i>
      </div></div>
    </li>
    <li v-else-if="curStatus === Status.empty">
      <div class="row"><div class="col-md-offset-2">
        <i class="empty">{{ message }}</i>
      </div></div>
    </li>
    <li v-else-if="curStatus === Status.error">
      <div class="row"><div class="col-md-4 col-sm-6">
        <div class="alert alert-danger">
          <h4><i class="icon fa fa-exclamation-triangle"></i> {{ message }}</h4>
        </div>
      </div></div>
    </li>
  </ul>
</div>
</template>

<script>
import timelineItem from "./timeline-item";
import axios from 'axios';

const Status = {open: 'open', loading: 'loading', success: 'success', empty: 'empty', error: 'error'};

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
      curStatus: Status.open,
      Status: Status,
      message: "",
      hasMore: false
    }
  },
  mounted: function() {
    this.curStatus = Status.loading;
    axios.get(this.action)
      .then(response => {
        this.curStatus = Status.success;
        this.items = response.data.items;
        this.hasMore = response.data.hasMore;
        if (this.items.length === 0) {
          this.message = response.data?.emptyMessage ? response.data.emptyMessage : 'Не найден';
          this.curStatus = Status.empty;
        }
      })
      .catch(error => {
        console.error(error.response);
        this.curStatus = Status.error;
        this.message = error.response.data.message;
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
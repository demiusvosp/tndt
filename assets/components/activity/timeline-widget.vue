<template>
<div class="activity-timeline">
  {{ subjectType}} - {{ subjectId }} from vue component<br/>
  <ul class="list-unstyled">
    <li v-for="item in items">
      <timeline-item v-bind="item"></timeline-item>
    </li>
  </ul>
</div>
</template>

<script>
import timelineItem from "./timeline-item";
export default {
  name: "timeline-widget",
  props: {
    subjectType: String,
    subjectId: String
  },
  components: {
    timelineItem
  },
  mounted: function() {
    console.log('subjectType:' + this.subjectType);
    console.log('subjectId:' + this.subjectId);
    console.log(this.items);
  },
  data: function () {
    return {
      items: [{"id":"018ce9f9-ba9d-73d1-8b87-e8cd57a55ad1","type":"task.changeState","createdAt":"2024-01-08T19:48:26+03:00","actor":"demius"},{"id":"018ce9f9-a5e9-7195-8410-48b3aef8e8a6","type":"comment.add","createdAt":"2024-01-08T19:48:21+03:00","actor":"demius"},{"id":"018ce9f9-88fd-7133-ba37-619497690a59","type":"task.changeState","createdAt":"2024-01-08T19:48:14+03:00","actor":"demius"},{"id":"018ce560-fc18-737b-a50c-0f5bee057952","type":"comment.add","createdAt":"2024-01-07T22:23:07+03:00","actor":"demius"},{"id":"018ce55f-35e9-71ad-b20f-10ec37cb2d8f","type":"task.changeState","createdAt":"2024-01-07T22:21:11+03:00","actor":"demius"}]
    }
  }
}
</script>

<style scoped>

</style>
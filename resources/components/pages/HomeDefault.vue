<template>
  <div>
    <a-row>
      <a-col :span="24">
        <a-card title="Home" style="margin-bottom: 10px">
          <a-col :md="24">
            Bienvenido
          </a-col>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import moment from "moment";

export default {
  name: "HomeDefault",
  data: () => ({
  }),
  components: {},
  mounted: function () {},
  methods: {
    moment,
  },
};
</script>
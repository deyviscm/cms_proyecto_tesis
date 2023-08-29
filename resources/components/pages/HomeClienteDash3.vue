<template>
  <div style="overflow: hidden;">
      
    <div class="container-iframe">
        <iframe title="Dashboard Enel 2.0" class="responsive-iframe" @load="load" width="100%" height="100%" src="https://app.powerbi.com/view?r=eyJrIjoiNTI3ZDNmMmMtZDZmZS00YTRkLWEwOTItMzBkMDYzYzZkZjVlIiwidCI6IjQxNTA1MGE4LWRjYmItNDU2ZC1iOWFlLTEyNDJiNGMxM2QxMSJ9&embedImagePlaceholder=true&pageName=ReportSection501376b21b83ad31088b" frameborder="0" allowFullScreen="true"></iframe>
    </div>

  </div>
</template>

<style>
.container-iframe {
  position: relative;
  overflow: hidden;
  width: 100%;
  /*padding-top: 56.25%;*/ /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
  padding-top: 59.41%;
  margin-top: -38px;
}

/* Then style the iframe to fit in the container div with full height and width */
.responsive-iframe {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  margin-top: 38px;
}
</style>

<script>

export default {
  name: "HomeClienteDash3",
  data: () => ({
  }),
  components: {
  },
  mounted: function () {
  },
  methods: {
      load(){
        //   alert('loaded');
      }
  },
};
</script>
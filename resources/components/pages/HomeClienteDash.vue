<template>
  <div style="overflow: hidden;">
      
    <div class="container-iframe">
        <!-- <iframe title="Dashboard Enel 2.0" class="responsive-iframe" @load="load" width="100%" height="100%" src="https://app.powerbi.com/view?r=eyJrIjoiODBiYWRjYjYtYTA4Yi00ZGE5LThjOWMtMWE3OTU0ODRiNTc5IiwidCI6ImE4MzE3NzZjLWM0ZTUtNDNhMC04ZmZhLTFkNjIxZWNlZDAzNiIsImMiOjl9&pageName=ReportSection501376b21b83ad31088b&embedImagePlaceholder=true" frameborder="0" allowFullScreen="true"></iframe> -->
    </div>
    <!-- <div style="text-align:center; margin-top:30px">
      <a-button type="primary" shape="round" size="sm" target="_blank" href="https://drive.google.com/uc?id=1CAig8PoyzvNweJCDIbE4QZIWW6DfAzZT&export=download">
        <a-icon type="download" />
        Anexos
      </a-button>
      <a-button type="primary" shape="round" size="sm" target="_blank" href="https://cdn.datanet.pe/enel-v3/digitalizacion.zip">
        <a-icon type="download" />
        Digitalización
      </a-button>
    </div> -->

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
  name: "HomeClienteDash",
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
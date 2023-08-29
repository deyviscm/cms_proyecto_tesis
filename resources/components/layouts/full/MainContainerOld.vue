<template>
  <div class="main-wrapper">
    <!---Navigation-->
    <Navbar :topbarColor="topbarColor" :logo="'/assets/images/logo/logo-light-icon.png'" :title="logotitle" />
    <!---Sidebar-->
    <SideBar parent=".main-wrapper" :sidebarLinks="sidebarLinks" />
    <!---Page Container-->
    <Main />
  </div>
</template>

<script>
import Navbar from './header/Navbar.vue';
import SideBar from "./sidebar/SideBar.vue";
import Main from "./main/Main.vue";
import sidebarLinks from "./sidebar/sidebarlinks.js";

export default {
  name: "MainContainer",
  components: {
    Navbar,
    SideBar,
    Main,
  },
  data: () => ({
    topbarColor: "#2962ff",
    logotitle: "Incognitos",
    sidebarLinks: sidebarLinks,
  }),
  mounted: function () {
    this.init();
  },
  methods: {
    init: function(){
    }
  },
};
</script>
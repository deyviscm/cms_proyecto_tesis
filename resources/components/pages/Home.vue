<template>
  <div>
    <HomeCliente></HomeCliente>
    <HomeClienteDash v-if="profile != 3"></HomeClienteDash>
    <HomeEvaluador v-if="profile == 3"></HomeEvaluador>
    <!-- <HomeDefault v-if="profile != 2 && profile != 3">Bienvenido</HomeDefault> -->
  </div>
</template>

<script>
import AuthService from '../../services/AuthService';
import HomeCliente from './HomeCliente';
import HomeClienteDash from './HomeClienteDash';
import HomeEvaluador from './HomeEvaluador';
import HomeDefault from './HomeDefault';

export default {
  name: "Home",
  data: () => ({
    profile: AuthService.profile()
  }),
  components: {
    HomeCliente,
    HomeClienteDash,
    HomeEvaluador,
    HomeDefault
  },
  mounted: function () {
  },
  methods: {
  },
};
</script>
<template>
  <div>
    <div style="height: 100%">
      <div style="width: 350px; margin: 0 auto; padding-top: 120px; padding-bottom:50px">
        <div style="box-shadow: rgb(196, 196, 196) 0px 0px 30px; border-radius:5px;overflow:hidden">
          <div style="background: #2020d8; text-align: center; padding: 40px 0">
            <img src="/assets/images/logo/logo.png?v=1.0" />
          </div>

          <div style="background: #fff; padding: 30px 30px 20px">
            <form @submit.prevent="submitLogin" method="post">
              <a-input
                v-model="formLogin.user"
                placeholder="Usuario"
                style="width: 100%; margin-bottom: 20px"
              >
                <a-icon slot="prefix" type="user" />
              </a-input>
              <a-input
                type="password"
                v-model="formLogin.password"
                placeholder="Contraseña"
                style="width: 100%; margin-bottom: 20px"
              >
                <a-icon slot="prefix" type="key" />
              </a-input>
              <a-button
                type="default"
                html-type="submit"
                style="width: 100%; margin-bottom: 20px"
                :disabled="formLogin.user == '' || formLogin.password == ''"
              >
                Ingresar
              </a-button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import AuthService from "../../services/AuthService";

export default {
  name: "Home",
  data: () => ({
    formLogin: {
      user: "",
      password: "",
    },
  }),
  components: {},
  methods: {
    submitLogin: function () {
      var _this = this;

      axios
        .post("/api/auth/login", this.formLogin)
        .then((response) => {
          if (AuthService.login(response.data)) {
            _this.$router.push("/home");
          } else {
            _this.$message.error("No se pudo iniciar sesión");
          }
        })
        .catch(function (error) {
          console.log(error);
          var message = "Error: " + error.message;
          if(Object.keys(error.response.status).length > 0 ){
            switch (error.response.status) {
              case 401:
                message = "Usuario y contraseña no válidos.";
            }
          }
          _this.$message.error(message);
        });
    },
  },
};
</script>
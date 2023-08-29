<template>
  <div>
    <a-row>
      <a-col :span="24">
        <a-card title="Evaluaciones" style="margin-bottom: 10px">
          <a-col :md="24">
            <a-button type="primary" @click="viewEvaluacion()"
              >Realizar Evaluación</a-button
            >

            <a-table :columns="columns" :data-source="data" style="margin-top:10px">
              <a slot="name" slot-scope="text">{{ text }}</a>
            </a-table>
          </a-col>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import moment from "moment";

export default {
  name: "Home",
  data: () => ({
    current: 1,
    columns: [
      {
        title: "Identificador",
        dataIndex: "nro_identificacion",
        key: "nro_identificacion",
      },
      {
        title: "Encuesta",
        dataIndex: "encuesta_id",
        key: "encuesta_id",
      },
      {
        title: "Fecha",
        dataIndex: "fecha",
        key: "fecha",
      },
      {
        title: "Usuario",
        dataIndex: "user.user",
        key: "user.user",
        ellipsis: true,
      },
    ],
    data: [],
  }),
  components: {},
  mounted: function () {
    this.init();
  },
  methods: {
    init() {
      axios
        .get("/api/encuesta")
        .then((response) => {
          this.data = response.data.data;
        })
        .catch((e) => AxiosGlobalError.error(e))
        .finally(() => {});
    },
    moment,

    viewEvaluacion() {
      this.$router.push("/realizar-evaluacion");
    },
  },
};
</script>
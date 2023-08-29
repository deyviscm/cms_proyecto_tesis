<template>
  <div>
    <a-row>
      <a-col :span="24">
        <a-card title="Usuarios" style="width: 100%">
          <a-button slot="extra" type="primary" @click="create">Crear</a-button>
          <a-table
            :columns="dataTable.columns"
            :data-source="dataTable.data"
            :pagination="dataTable.pagination"
            :loading="dataTable.loading"
            :row-key="(record) => record.id"
            :scroll="{ y: 350 }"
            @change="handleTableChange"
            size="small"
          >
            <span slot="action" slot-scope="obj">
              <a href="javascript:;">
                <a-icon
                  @click="modify(obj.id)"
                  type="eye"
                  class="feather-sm"
                ></a-icon>
              </a>

              <a-popconfirm
                placement="left"
                title="¿Desea eliminar?"
                ok-text="Si"
                cancel-text="No"
                @confirm="remove(obj.id)"
              >
                <a href="javascript:;">
                  <a-icon type="delete" class="feather-sm"></a-icon>
                </a>
              </a-popconfirm>
            </span>
          </a-table>
        </a-card>
      </a-col>
    </a-row>

    <a-modal
      :title="formCrud.id == null ? 'Registro':'Modificación'"
      :visible="modalForm.visible"
      :confirm-loading="modalForm.confirmLoading"
      ok-text="Guardar"
      cancel-text="Cancelar"
      @ok="handleOk"
      @cancel="handleCancel"
      width="600px"
      size="lg"
    >
      <a-spin :spinning="modalForm.dataLoading">
        <a-row :gutter="24">
          <a-col :md="12">
            <div class="form-v">
              <label>PERFIL</label>
              <a-select
                placeholder="Seleccione"
                optionFilterProp="children"
                v-model="formCrud.perfil"
                style="width: 100%"
              >
                <a-select-option
                  v-for="item in options.perfiles"
                  :key="item.id"
                  :value="item.id"
                >
                  {{ item.nombre }}
                </a-select-option>
              </a-select>
            </div>
          </a-col>
          <a-col :md="12">
            <div class="form-v">
              <label>USUARIO</label>
              <a-input v-model="formCrud.usuario"></a-input>
            </div>
          </a-col>
          <a-col :md="24">
            <div class="form-v">
              <label>NOMBRE</label>
              <a-input v-model="formCrud.nombre"></a-input>
            </div>
          </a-col>
          <a-col :md="24">
            <div class="form-v">
              <label>EMAIL</label>
              <a-input v-model="formCrud.email"></a-input>
            </div>
          </a-col>
          <a-col :md="12">
            <div class="form-v">
              <label>CONTRASEÑA</label>
              <a-input v-model="formCrud.password" type="password"></a-input>
            </div>
          </a-col>
          <a-col :md="12">
            <div class="form-v">
              <label>REPITE CONTRASEÑA</label>
              <a-input
                v-model="formCrud.password_confirmation"
                type="password"
              ></a-input>
            </div>
          </a-col>
        </a-row>
      </a-spin>
    </a-modal>
  </div>
</template>

<script>
import AxiosGlobalError from "../../services/AxiosGlobalError";

export default {
  name: "Usuarios",
  data: () => ({
    dataTable: {
      data: [],
      pagination: {},
      sorter: {},
      loading: false,
      columns: [
        {
          title: "Usuario",
          dataIndex: "user",
          sorter: true,
        },
        {
          title: "Perfil",
          dataIndex: "perfil_nombre",
        },
        {
          title: "Acción",
          align: "center",
          width: 100,
          scopedSlots: { customRender: "action" },
        },
      ],
    },
    modalForm: {
      visible: false,
      confirmLoading: false,
      dataLoading: false,
    },
    formCrud: {
      id: null,
      perfil: "",
      usuario: "",
      nombre: "",
      email: "",
      password: "",
      password_confirmation: "",
    },
    options: {
      perfiles: [],
    },
  }),
  mounted: function () {
    this.init();
  },
  watch: {
    $route(to, from) {
      this.init();
    },
  },
  methods: {
    init: function () {
      this.fetch();
      this.dataForm();
    },
    dataForm() {
      this.options.perfiles = [];
      axios
        .get("/api/usuario/form")
        .then((response) => {
          this.options.perfiles = response.data.data.perfiles;
        })
        .catch((e) => AxiosGlobalError.error(e))
        .finally(() => {});
    },
    handleTableChange(pagination, filters, sorter) {
      var tbl = this.dataTable;
      const pager = { ...tbl.pagination };
      pager.current = pagination.current;
      tbl.pagination = pager;
      tbl.sorter = sorter;
      this.fetch({
        results: pagination.pageSize,
        page: pagination.current,
        sortField: sorter.field,
        sortOrder: sorter.order,
        ...filters,
      });
    },
    fetch(params = {}) {
      var tbl = this.dataTable;
      tbl.loading = true;
      tbl.pagination.current = params.page || 1;
      tbl.pagination.pageSize = 25;
      params = { results: tbl.pagination.pageSize, ...params };
      axios
        .get("/api/usuario", { params })
        .then((response) => {
          const pagination = { ...tbl.pagination };
          pagination.total = response.data.data.total;
          tbl.pagination = pagination;
          tbl.data = response.data.data.data;
        })
        .catch((e) => AxiosGlobalError.error(e))
        .finally(() => (tbl.loading = false));
    },
    resetForm() {
      this.formCrud.id = null;
      this.formCrud.perfil = null;
      this.formCrud.usuario = "";
      this.formCrud.nombre = "";
      this.formCrud.email = "";
      this.formCrud.password = "";
      this.formCrud.password_confirmation = "";
    },
    create() {
      this.resetForm();
      this.modalForm.visible = true;
    },
    modify(id) {
      this.resetForm();
      this.formCrud.id = id;
      this.modalForm.visible = true;

      this.modalForm.dataLoading = true;
      axios
        .get("/api/usuario/info/" + id)
        .then((response) => {
          this.formCrud.perfil = response.data.data.perfil_id;
          this.formCrud.usuario = response.data.data.user;
          this.formCrud.nombre = response.data.data.name;
          this.formCrud.email = response.data.data.email;
        })
        .catch((e) => AxiosGlobalError.error(e))
        .finally(() => {
          this.modalForm.dataLoading = false;
        });
    },
    handleOk(e) {
      this.modalForm.confirmLoading = true;

      if (this.formCrud.id == null) {
        axios
          .post("/api/usuario/create", this.formCrud)
          .then((response) => {
            this.fetch({
              pageSize: this.dataTable.pagination.pageSize,
              page: this.dataTable.pagination.current,
              sortField: this.dataTable.sorter.field,
              sortOrder: this.dataTable.sorter.order,
            });
            this.$message.success("Datos registrados");
            this.modalForm.visible = false;
          })
          .catch((e) => AxiosGlobalError.error(e))
          .finally(() => {
            this.modalForm.confirmLoading = false;
          });
      } else {
        axios
          .post("/api/usuario/update", this.formCrud)
          .then((response) => {
            this.fetch({
              pageSize: this.dataTable.pagination.pageSize,
              page: this.dataTable.pagination.current,
              sortField: this.dataTable.sorter.field,
              sortOrder: this.dataTable.sorter.order,
            });
            this.$message.success("Datos actualizados");
            this.modalForm.visible = false;
          })
          .catch((e) => AxiosGlobalError.error(e))
          .finally(() => {
            this.modalForm.confirmLoading = false;
          });
      }
    },
    handleCancel(e) {
      this.modalForm.visible = false;
    },
    remove(id) {
      axios
        .post("/api/usuario/remove", { id: id })
        .then((response) => {
          this.fetch({
            pageSize: this.dataTable.pagination.pageSize,
            page: this.dataTable.pagination.current,
            sortField: this.dataTable.sorter.field,
            sortOrder: this.dataTable.sorter.order,
          });
          this.$message.success("Registro eliminado");
        })
        .catch((e) => AxiosGlobalError.error(e))
        .finally(() => {});
    },
  },
};
</script>
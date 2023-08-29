<style>
	.md-modal .ant-modal-body{
		padding-top: 0;
	}
	.img-dwl{
		padding-top: 30px;
		cursor: pointer;
		color: #004389;
	}
</style>
<template>
	<div>
	  <a-row>
		<a-col :span="24">
		  <a-card title="Ordenes de Compras" style="width: 100%">
			<!-- <a-button slot="extra" type="primary" @click="create">Crear</a-button> -->
			
			<form @submit.prevent="searchList">
				<a-row :gutter="24">
					<a-col :md="4">
						<div class="form-v">
							<label>Nro Oden</label>
							<a-input v-model="filter.nro_orden"></a-input>
						</div>
					</a-col>
					<a-col :md="4">
						<div class="form-v">
							<label>Desde</label>
							<a-date-picker style="width: 100%;"
							v-model="filter.startDate"
							:disabled-date="disabledStartDate"
							format="YYYY-MM-DD"
							placeholder="Start"
							@openChange="handleStartOpenChange"
							/>
						</div>
					</a-col>
					<a-col :md="4">
						<div class="form-v">
							<label>Hasta</label>
							<a-date-picker style="width: 100%;"
							v-model="filter.endDate"
							:disabled-date="disabledEndDate"
							format="YYYY-MM-DD"
							placeholder="End"
							:open="endOpenDate"
							@openChange="handleEndOpenChange"
							/>
						</div>
					</a-col>
					<a-col :md="4">
						<div class="form-v">
							<label>ESTADO</label>
							<a-select placeholder="Seleccione" optionFilterProp="children" v-model="filter.estado" style="width: 100%">
								<a-select-option value="">Todos</a-select-option>
								<a-select-option v-for="item in options.estados" :key="item.codigo" :value="item.codigo">
									{{ item.descripcion }}
								</a-select-option>
							</a-select>
						</div>
					</a-col>
					<a-col :md="6">
						<div class="form-v">
							<a-button style="margin-top: 17px;" slot="extra" type="primary" html-type="submit">Buscar</a-button>
							<a-button style="margin-top: 17px;" class="btn-success" @click="downloadExcelOV()">Excel</a-button>
							<!-- <a-button style="margin-top: 17px;" type="danger">PDF</a-button> -->
						</div>
					</a-col>
				</a-row>
			</form>


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
				<span slot="st_total" slot-scope="obj">
					{{obj.total}}
				</span>
				<span slot="action" slot-scope="obj">
					<a href="javascript:;">
						<a-icon
							@click="modify(obj.id)"
							type="eye"
							class="feather-sm"
						></a-icon>
					</a>
				</span>
			</a-table>
		  </a-card>
		</a-col>
	  </a-row>
	  <!-- ############################################################################################## -->
	  <!-- ################################### MODAL FORM ############################################### -->
	  <!-- ############################################################################################## -->
		<a-modal
		:title="formCrud.id == null ? 'Registro':'Modificación'"
		:visible="modalForm.visible"
		:confirm-loading="modalForm.confirmLoading"
		ok-text="Guardar"
		cancel-text="Cancelar"
		@ok="handleOk"
		@cancel="handleCancel"
		width="1000px"
		size="lg"
		:dialog-style="{ top: '20px' }"
		class="md-modal"
		>
			<a-spin :spinning="modalForm.dataLoading">
				<a-row :gutter="24">
					<a-tabs default-active-key="1">
						<a-tab-pane key="1" tab="Información">
							<a-row :gutter="16">
								<a-col :sm="24" :md="24" :lg="24">
									<fieldset class="border pd-10 mb-2">
										<legend class="w-auto text-muted tx-12 font-weight-bold">Datos</legend>
										<a-col :md="4">
											<div class="form-v">
												<label>Nro Orden</label>
												<a-input v-model="formCrud.nro_orden" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="6">
											<div class="form-v">
												<label>Nombre</label>
												<a-input v-model="formCrud.nombre" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="6">
											<div class="form-v">
												<label>Apellidos</label>
												<a-input v-model="formCrud.apellidos" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="4">
											<div class="form-v">
												<label>Tipo de Documento</label>
												<a-select :disabled="true" placeholder="Seleccione" v-model="formCrud.tipo_comprobante" style="width: 100%">
													<a-select-option value="1">Boleta</a-select-option>
													<a-select-option value="2">Factura</a-select-option>
												</a-select>
											</div>
										</a-col>
										<a-col :md="4">
											<div class="form-v">
												<label>Nro Documento</label>
												<a-input v-model="formCrud.nro_documento" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="4">
											<div class="form-v">
												<label>Fecha Compra</label>
												<a-input v-model="formCrud.fecha_compra" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="6">
											<div class="form-v">
												<label>Empresa</label>
												<a-input v-model="formCrud.empresa" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="4">
											<div class="form-v">
												<label>Celular</label>
												<a-input v-model="formCrud.celular" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="4">
											<div class="form-v">
												<label>Estado</label>
												<a-select placeholder="Seleccione" optionFilterProp="children" v-model="formCrud.estado_pedido" style="width: 100%">
													<a-select-option v-for="item in options.estados" :key="item.codigo" :value="item.codigo">
														{{ item.descripcion }}
													</a-select-option>
												</a-select>
											</div>
										</a-col>
										<a-col :md="6">
											<div class="form-v">
												<label>Email</label>
												<a-input v-model="formCrud.email" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="4">
											<div class="form-v">
												<label>Total</label>
												<a-input v-model="formCrud.total" :disabled="true"></a-input>
											</div>
										</a-col>
									</fieldset>
								</a-col>
							</a-row>
							<a-row :gutter="16">
								<a-col :sm="24" :md="24" :lg="24">
									<fieldset class="border pd-10 mb-2">
										<legend class="w-auto text-muted tx-12 font-weight-bold">Ubigeo</legend>
										<a-col :md="8">
											<div class="form-v">
												<label>Dirección</label>
												<a-select 
												:disabled="true"
												show-search
												allowClear
												placeholder="Selecciona Dirección"
												:filterOption="false"
												optionFilterProp="filterProp" 
												v-model="formCrud.ubigeo" 
												@search="buscarUbigeoFiltro"
												:notFoundContent="'No se encontro la dirección'"
												style="width: 100%"
												>
													<a-select-option v-for="item in options.ubigeosFiltro" :key="item.ubigeo" :value="item.ubigeo">
														{{ item.descripcion }}
													</a-select-option>
												</a-select>
											</div>
										</a-col>
										<a-col :md="4">
											<div class="form-v">
												<label>Calle</label>
												<a-input v-model="formCrud.calle_direccion" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="4">
											<div class="form-v">
												<label>Número</label>
												<a-input v-model="formCrud.numero_direccion" :disabled="true"></a-input>
											</div>
										</a-col>
										<a-col :md="8">
											<div class="form-v">
												<label>Referencia</label>
												<a-input v-model="formCrud.referencia_direccion" :disabled="true"></a-input>
											</div>
										</a-col>
									</fieldset>
								</a-col>
							</a-row>
							<a-row :gutter="16">
								<a-col :sm="24" :md="24" :lg="24">
									<fieldset class="border pd-10 mb-2">
										<legend class="w-auto text-muted tx-12 font-weight-bold">Medio de Pago</legend>
										<a-col :md="12">
											<div class="form-v">
												<label>Tipo de Pago</label>
												<a-select :disabled="true" placeholder="Seleccione" optionFilterProp="children" v-model="formCrud.tipo_pago" style="width: 100%">
													<a-select-option v-for="item in options.tipoPagos" :key="item.codigo" :value="item.codigo">
														{{ item.descripcion }}
													</a-select-option>
												</a-select>
											</div>
										</a-col>
										<a-col :md="12" v-if="formCrud.tipo_pago == '1'">
											<a-tooltip title="Descargar">
												<a-avatar @click="downloadFile()" style="border: 1px solid #003059;cursor: pointer;" :size="64" shape="square" :src="options.url_web+'/public/images/imagen_transferencia/'+formCrud.id+'/'+formCrud.imagen_tranferencia" />
												<!-- <i class="fa fa-download fa-lg img-dwl" aria-hidden="true" @click="downloadFile()"></i> -->
											</a-tooltip>
										</a-col>
									</fieldset>
								</a-col>
							</a-row>
						</a-tab-pane>
							<!-- ############################################# -->
							<!-- ############## Productos ################### -->
							<!-- ############################################# -->
						<a-tab-pane key="2" tab="Productos">
							<a-row :gutter="16">
								<a-col :sm="24" :md="24" :lg="24">
									<a-table 
									:columns="detalleProductos.columns" 
									:data-source="detalleProductos.data"
			  						size="small">
									  	<span slot="st_url" slot-scope="obj">
											<img width="70" :src="obj.url_producto">
										</span>
									</a-table>
								</a-col>
							</a-row>
						</a-tab-pane>
					</a-tabs>
				</a-row>
			</a-spin>
		</a-modal>
	</div>
</template>
  
  <script>
import moment from "moment";
import AxiosGlobalError from "../../services/AxiosGlobalError";
  
  export default {
	name: "Usuarios",
	data: () => ({
		endOpenDate: false,
		filter:{
			nro_orden : '',
			startDate : undefined,
			endDate : undefined,
			estado: 1
		},
		dataTable: {
			data: [],
			pagination: {},
			sorter: {},
			loading: false,
			columns: [
					{
						title: "Nro Orden",
						dataIndex: "nro_orden",
						// sorter: true,
					},
					{
						title: "Cliente",
						dataIndex: "cliente",
					},
					{
						title: "Tipo Pago",
						dataIndex: "tipo_pago_desc",
					},
					{
						title: "Productos",
						dataIndex: "total_productos",
					},
					{
						title: "Moneda",
						dataIndex: "simbolo",
						align: "center",
					},
					{
						title: "Total",
						// dataIndex: "total",
						align: "center",
						scopedSlots: { customRender: "st_total"}
					},
					{
						title: "Estado",
						dataIndex: "desc_estado",
					},
					{
						title: "Fecha Registro",
						dataIndex: "fecha_compra",
						align: "center",
					},
					{
						title: "Acción",
						align: "center",
						width: 100,
						scopedSlots: { customRender: "action" },
					},
				],
		},
		detalleProductos:{
			data : [],
			columns:[
				{
					title: "Producto",
					// dataIndex: "url_producto",
					align: "center",
					scopedSlots: { customRender: "st_url"}
				},
				{
					title: "Descripción",
					dataIndex: "producto",
				},
				{
					title: "P.Unitario",
					dataIndex: "precio_unitario",
					align: "center",
				},
				{
					title: "Cantidad",
					dataIndex: "cantidad",
					align: "center",
				},
				{
					title: "Total",
					dataIndex: "total",
					align: "total",
				},
			]
		},
		modalForm: {
			visible: false,
			confirmLoading: false,
			dataLoading: false,
		},
		formCrud: {
			id: null,
			nombre: "",
			apellidos: "",
			nro_orden: "",
			empresa: "",
			celular: "",
			email: "",
			ubigeo: "",
			calle_direccion: "",
			numero_direccion: "",
			referencia_direccion: "",
			tipo_comprobante: "",
			nro_documento: "",
			tipo_pago: "",
			id_cuenta_bancaria: "",
			imagen_transferencia: "",
			id_moneda: "",
			subtotal: "",
			envio_pedido: "",
			total: "",
			estado_pedido: "",
			fecha_compra: "",
		},
		formDetalle:[],
		options: {
			perfiles: [],
			estados: [],
			ubigeos: [],
			ubigeosFiltro: [],
			url_web: '',
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
			this.filter.startDate = moment().subtract(1, 'year');
			this.filter.endDate = moment();
			this.fetch();
			this.dataForm();
		},
		dataForm() {
			// this.options.perfiles = [];
			axios
			.get("/api/ordenes-compras/form")
			.then((response) => {
				// this.options.perfiles = response.data.data.perfiles;
				this.options.estados = response.data.data.estados;
				this.options.ubigeos = response.data.data.ubigeos;
				this.options.ubigeosFiltro = response.data.data.ubigeos;
				this.options.tipoPagos = response.data.data.tipoPagos;
				this.options.url_web = response.data.data.url_web;
			})
			.catch((e) => AxiosGlobalError.error(e))
			.finally(() => {});
		},
		searchList(){
			this.dataTable.pagination.current = 1;
			this.fetch({
				pageSize: this.dataTable.pagination.pageSize,
				page: this.dataTable.pagination.current,
				sortField: this.dataTable.sorter.field,
				sortOrder: this.dataTable.sorter.order,
			});
		},
		handleTableChange(pagination, filters, sorter) {
			console.log(pagination);
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
			tbl.pagination.current = params.page || 1;
			tbl.pagination.pageSize = 10;

			var desde = (this.filter.startDate !== undefined && this.filter.startDate != null) ? moment(this.filter.startDate).format('YYYY-MM-DD') : '';
			var hasta = (this.filter.endDate !== undefined && this.filter.endDate != null) ? moment(this.filter.endDate).format('YYYY-MM-DD') : '';
			if(desde == '' || hasta == ''){
				this.$message.error("Seleccione un rango de fechas."); return false;
			}
			var filter = {
				nro_orden: this.filter.nro_orden,
				estado: this.filter.estado,
				startDate: desde,
				endDate: hasta,
			};
			tbl.loading = true;
			params = { results: tbl.pagination.pageSize, ...params, ...filter};
			axios
			.get("/api/ordenes-compras", { params })
			.then((response) => {
				const pagination = { ...tbl.pagination };
				pagination.total = response.data.data.total;
				tbl.pagination = pagination;
				console.log(tbl.pagination,'tbl.pagination')
				tbl.data = response.data.data.data;
			})
			.catch((e) => AxiosGlobalError.error(e))
			.finally(() => (tbl.loading = false));
		},
		resetForm() {
			this.formCrud.id = null;
			this.formCrud.nombre = '';
			this.formCrud.apellidos = '';
			this.formCrud.nro_orden = '';
			this.formCrud.empresa = '';
			this.formCrud.celular = '';
			this.formCrud.email = '';
			this.formCrud.ubigeo = '';
			this.formCrud.calle_direccion = '';
			this.formCrud.numero_direccion = '';
			this.formCrud.referencia_direccion = '';
			this.formCrud.tipo_comprobante = '';
			this.formCrud.nro_documento = '';
			this.formCrud.tipo_pago = '';
			this.formCrud.id_cuenta_bancaria = '';
			this.formCrud.imagen_transferencia = '';
			this.formCrud.id_moneda = '';
			this.formCrud.subtotal = '';
			this.formCrud.envio_pedido = '';
			this.formCrud.total = '';
			this.formCrud.estado_pedido = '';
			this.formCrud.fecha_compra = '';
			this.detalleProductos.data = [];
		},
		buscarUbigeoFiltro: function(term){
			if (term !== undefined && term.trim().length >= 2){
				let pos = 1;
				this.options.ubigeosFiltro = this.options.ubigeos.filter((e) => {
					let dataFormat = e.descripcion;
					return (dataFormat.toLowerCase().indexOf(term.toLowerCase()) !== -1 && pos++ <= 10);
				});
				// console.log(term,'term')
			}else{
				this.options.ubigeosFiltro = [];
			}
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
			.get("/api/ordenes-compras/info/" + id)
			.then((response) => {
				this.formCrud = response.data.data;
				this.formCrud.total = this.formCrud.simbolo + ' ' + this.formCrud.total.toFixed(2);
				this.detalleProductos.data = response.data.detalle;
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
				.post("/api/ordenes-compras/create", this.formCrud)
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
				.post("/api/ordenes-compras/update", this.formCrud)
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
		disabledStartDate(startValue) {
			const endValue = this.filter.endDate;
			if (!startValue || !endValue) {
				return false;
			}
			return startValue.valueOf() > endValue.valueOf();
		},
		disabledEndDate(endValue) {
			const startValue = this.filter.startDate;
			if (!endValue || !startValue) {
				return false;
			}
			return startValue.valueOf() >= endValue.valueOf();
		},
		handleStartOpenChange(open) {
			if (!open) {
				this.endOpenDate = true;
			}
		},
		handleEndOpenChange(open) {
			this.endOpenDate = open;
		},
		downloadFile() {
			let params = {
				imagen : this.formCrud.imagen_tranferencia,
				id: this.formCrud.id,
			}
			this.modalForm.dataLoading = true;
			axios
			.post("/api/ordenes-compras/descargar",params)
			.then((response) => {
				console.log(response)
				let data = response.data;
				let url = data.url;
				let fileName = data.fileName;
				this.descargarFile(url, fileName);
			})
			.catch((e) => AxiosGlobalError.error(e))
			.finally(() => {
				this.modalForm.dataLoading = false;
			});
		},
		downloadExcelOV() {
			var desde = (this.filter.startDate !== undefined && this.filter.startDate != null) ? moment(this.filter.startDate).format('YYYY-MM-DD') : '';
			var hasta = (this.filter.endDate !== undefined && this.filter.endDate != null) ? moment(this.filter.endDate).format('YYYY-MM-DD') : '';
			if(desde == '' || hasta == ''){
				this.$message.error("Seleccione un rango de fechas."); return false;
			}
			var filter = {
				nro_orden: this.filter.nro_orden,
				estado: this.filter.estado,
				startDate: desde,
				endDate: hasta,
			};
			this.modalForm.dataLoading = true;
			axios
			.post("/api/ordenes-compras/descargar-excel",filter)
			.then((response) => {
				// console.log(response)
				let data = response.data;
				let url = data.url;
				let fileName = data.fileName;
				this.descargarFile(url, fileName);
			})
			.catch((e) => AxiosGlobalError.error(e))
			.finally(() => {
				this.modalForm.dataLoading = false;
			});
		},
		descargarFile: function(uri, fileName){
            var link = document.createElement('a');
            if(typeof link.download === 'string'){
                document.body.appendChild(link);
                link.download = fileName;
                link.href = uri;
                link.click();
                document.body.removeChild(link);
            }else{
                location.replace(uri)
            }
        }
	},
  };
  </script>
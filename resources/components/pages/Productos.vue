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
			<a-card title="Productos" style="width: 100%">
				<a-button slot="extra" type="primary" @click="create">Crear</a-button>
				
				<a-row :gutter="24">
					<form @submit.prevent="searchList">
						<a-col :md="4">
							<div class="form-v">
								<label>Nombre</label>
								<a-input v-model="filter.nombre"></a-input>
							</div>
						</a-col>
						<a-col :md="6">
							<div class="form-v">
								<label>Categoria</label>
								<a-select placeholder="Seleccione" optionFilterProp="children" v-model="filter.categoria" style="width: 100%">
									<a-select-option value="">Todos</a-select-option>
									<a-select-option v-for="item in options.categorias" :key="item.id" :value="item.id">
										{{ item.nombre }}
									</a-select-option>
								</a-select>
							</div>
						</a-col>
						<a-col :md="4">
							<div class="form-v">
								<a-button style="margin-top: 17px;" class="primary" type="primary" html-type="submit" >Buscar</a-button>
							</div>
						</a-col>
					</form>
				</a-row>


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
					
					<span slot="str_url" slot-scope="obj">
						<a target="_blank" :href="options.url_web+'/productos/'+obj.categoria_url+'/'+obj.url">Url Link</a>
					</span>
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
							<a-icon style="color: red;" type="delete" class="feather-sm"></a-icon>
							</a>
						</a-popconfirm>
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
		width="700px"
		size="lg"
		:dialog-style="{ top: '20px' }"
		class="md-modal"
		>
			<a-spin :spinning="modalForm.dataLoading">
				<a-row :gutter="24">
					<a-tabs v-model="tab_key">
						<a-tab-pane key="1" tab="Información">
							<a-row :gutter="16">
								<a-col :sm="24" :md="24" :lg="24">
									<fieldset class="border pd-10 mb-2">
										<legend class="w-auto text-muted tx-12 font-weight-bold">Datos</legend>
										<a-col :md="12">
											<div class="form-v">
												<label>Nombre</label>
												<a-input v-model="formCrud.titulo"></a-input>
											</div>
										</a-col>
										<a-col :md="12">
											<div class="form-v">
												<label>Categoria</label>
												<a-select placeholder="Seleccione" optionFilterProp="children" v-model="formCrud.categoria_id" style="width: 100%">
													<a-select-option v-for="item in options.categorias" :key="item.id" :value="item.id">
														{{ item.nombre }}
													</a-select-option>
												</a-select>
											</div>
										</a-col>
										<a-col :md="6">
											<div class="form-v">
												<label>Moneda</label>
												<a-select v-model="formCrud.id_moneda" style="width: 100%">
													<a-select-option v-for="item in options.moneda" :key="item.id" :value="item.id" selected>
														{{ item.descripcion }}
													</a-select-option>
												</a-select>
											</div>
										</a-col>
										<a-col :md="6">
											<div class="form-v">
												<label>Precio Unitario</label>
												<a-input v-model="formCrud.precio_unitario" @keypress="isNumber($event, formCrud.precio_unitario)"></a-input>
											</div>
										</a-col>
										<a-col :md="24">
											<div class="form-v">
												<label>Descripción</label>
												<a-textarea v-model="formCrud.descripcion" :row="4"></a-textarea>
											</div>
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
									<a-col :sm="24" :md="24" :lg="24" class="mb-2">
										<a-button type="primary" @click="loadImage()">Agregar Imagen</a-button>
									</a-col>
									<a-col :sm="24" :md="24" :lg="24" class="mb-2">
										<a-table 
										:columns="detalleImg.columns" 
										:data-source="detalleImg.data"
										:row-key="(record) => record.url"
										size="small">
											<span slot="str_url_imagen" slot-scope="obj">
												<img width="70" :src="obj.url_imagen">
											</span>
											<span slot="str_principal" slot-scope="obj">
												<a-switch v-model="obj.principal" @change="onChangePrincipal(obj)" size="small" />
											</span>
											<span slot="action" slot-scope="obj">
												<a-popconfirm
													placement="left"
													title="¿Desea eliminar?"
													ok-text="Si"
													cancel-text="No"
													@confirm="removeImage(obj)"
												>
													<a href="javascript:;">
													<a-icon type="delete" class="feather-sm"></a-icon>
													</a>
												</a-popconfirm>
											</span>
										</a-table>
									</a-col>
								</a-col>
							</a-row>
						</a-tab-pane>
					</a-tabs>
				</a-row>
			</a-spin>
		</a-modal>

		<a-modal
		:title="'ADJUNTAR IMAGEN'"
		:visible="modalFormImg.visible"
		:confirm-loading="modalFormImg.confirmLoading"
		ok-text="Guardar"
		cancel-text="Cancelar"
		@ok="handleOkImage"
		@cancel="handleCancelImage"
		width="500px"
		size="lg"
		:dialog-style="{ top: '20px' }"
		class="md-modal"
		>
			<a-row :gutter="8" class="mt-3">
				<a-col :sm="24" :md="24" :lg="24">
					<a-upload-dragger
							class="mg-10"
							name="file"
							:multiple="true"
							action="api/archivos/guardarArchivo"
							@change="handleChangeArchivo"
							:beforeUpload="beforeUpload"
							:showUploadList="false"
							:disabled="disabled.upload"
					>
						<p class="ant-upload-drag-icon">
							<a-icon type="inbox" />
						</p>
						<p class="ant-upload-text" style="margin-bottom: 0;">Clickea o Arrastra</p>
						<p class="ant-upload-hint">Soporta uno o varios Archivos</p>
					</a-upload-dragger>
					<!-- Lista de documentos -->
					<span class="mg-10">
						<div style="text-align:center;" v-if="loadingDropZoneDocumentos > 0">
							<i class="fas fa-spinner fa-spin fa-lg" style="color: #17881f;"></i> <em>Cargando:  <span v-if="loadingDropZoneDocumentos === 1">1 documento restante.</span><span v-else>@{{ loadingDropZoneDocumentos }} documentos restantes.</span></em>
						</div>
						<a-form class="ant-advanced-search-form">
							<a-row :gutter="0" v-for="(documento, index) in documentos" :key="index" class="list-files" style="padding-top: 4px; padding-bottom: 4px">
								<a-col :sm="12" :md="12" :lg="12">
									<span style="padding-left: 4px; padding-right: 4px" :title="((documento.mensajeErrorInput !== undefined) ? `Mensaje de error: ${documento.mensajeErrorInput}` : `Archivo: ${documento.nombre}`)" :class="{ 'text-danger' : (documento.mensajeErrorInput !== undefined ) }">
										<i aria-label="icon: paper-clip" class="anticon anticon-paper-clip mr-1" :class="{ 'text-danger' : (documento.mensajeErrorInput !== undefined), 'text-muted': !(documento.mensajeErrorInput !== undefined ) }"></i> {{ wrapText(documento.name, 20) }}
									</span>
								</a-col>
								<!-- <a-col :sm="12" :md="12" :lg="5" class="pl-2 pr-2">
									<a-select
										size="small"
										v-model="documento.tipoDocumento"
										showSearch
										style="width: 100%"
										placeholder="-SELECCIONE TIPO DE DOCUMENTO-"
										:filterOption="filterOpciones"
									>
										<a-select-option v-for="item in tipoDocumentosExcel" :key="item.id_doc">
											@{{ item.descripcion.toUpperCase() }} 
										</a-select-option>
									</a-select>
								</a-col> -->
								<a-col class="pl-2 pr-1">
									<a-popconfirm
											placement="left"
											title="¿Desea eliminar?"
											ok-text="Si"
											cancel-text="No"
											@confirm="eliminarDocumentoDropzoneSubidaDocumentos(index)"
										>
										<a class="float-right" href="javascript:void(0);" title="Eliminar Archivo" type="danger" ><a-icon :style="{ fontSize: '17px'}" type="delete" class="feather-sm-d hov-feather"/></a>
									</a-popconfirm>
									
								</a-col>
							</a-row>
						</a-form>
					</span>
				</a-col>
			</a-row>
			<!-- <a-spin :spinning="modalForm.dataLoading">
			</a-spin> -->
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
			nombre : '',
			categoria: '',
		},
		dataTable: {
			data: [],
			pagination: {},
			sorter: {},
			loading: false,
			columns: [
					{
						title: "Nombre",
						dataIndex: "titulo",
						// sorter: true,
					},
					{
						title: "Moneda",
						dataIndex: "moneda_simbolo",
						width: 100,
						align: "center",
						// sorter: true,
					},
					{
						title: "Precio Unitario",
						dataIndex: "precio_unitario",
						width: 150,
						align: "center",
						// sorter: true,
					},
					{
						title: "Categoria",
						dataIndex: "categoria_nombre",
						// sorter: true,
					},
					{
						title: "URL",
						// dataIndex: "url",
						width: 100,
						scopedSlots: { customRender: "str_url" },
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
			titulo: "",
			id_categoria: "",
			categoria_id: '',
			id_moneda: 1,
			precio_unitario: '',
			descripcion: '',
		},
		detalleImg:{
			data : [],
			columns:[
				{
					title: "Imagen",
					scopedSlots: { customRender: "str_url_imagen" },
				},
				{
					title: "Principal",
					scopedSlots: { customRender: "str_principal" },
				},
				{
					title: "Acción",
					align: "center",
					width: 100,
					scopedSlots: { customRender: "action" },
				},
			],
		},
		options: {
			perfiles: [],
			categorias : [],
			moneda : [],
			url_web: '',
		},
		url_web : '',
		modalFormImg:{
			visible: false,
			confirmLoading: false,
			dataLoading: false,
		},
		disabled:{
			upload: false
		},
		documentos: [],
		documentos_remove: [],
		loadingDropZoneDocumentos : 0,
		tab_key: '1',
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

			axios
			.get("/api/productos/form")
			.then((response) => {
				this.options.perfiles = response.data.data.perfiles;
				this.options.categorias = response.data.data.categorias;
				this.options.moneda = response.data.data.moneda;
				this.options.url_web = response.data.data.url;
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

			var filter = {
				categoria: this.filter.categoria,
				nombre: this.filter.nombre,
			};
			tbl.loading = true;
			params = { results: tbl.pagination.pageSize, ...params, ...filter};
			axios
			.get("/api/productos", { params })
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
			this.formCrud.id= null;
			this.formCrud.titulo= "";
			this.formCrud.id_categoria= "";
			this.formCrud.id_moneda= 1;
			this.formCrud.precio_unitario= '';
			this.formCrud.descripcion= '';
			this.formCrud.categoria_id= "";
			this.detalleImg.data = [];
			this.tab_key = '1';
		},
		create() {
			this.resetForm();
			this.modalForm.visible = true;
		},
		remove(id) {
			axios
			.post("/api/productos/remove", { id: id })
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
		modify(id) {
			this.resetForm();
			this.formCrud.id = id;
			this.modalForm.visible = true;
	
			this.modalForm.dataLoading = true;
			axios
			.get("/api/productos/info/" + id)
			.then((response) => {
				this.formCrud = response.data.data;
				this.detalleImg.data = response.data.imagenes;
				this.detalleImg.data.forEach((r, i) =>{
					r.principal = (r.principal == 1) ? true : false;
				});
			})
			.catch((e) => AxiosGlobalError.error(e))
			.finally(() => {
				this.modalForm.dataLoading = false;
			});
		},
		handleOk(e) {
			if(this.detalleImg.data.length > 0){
				var validate = true;
				this.detalleImg.data.forEach((r, i) => {
					if(r.principal == true){
						validate = false;
					}
				});
				if(validate){
					this.$message.error("Debe seleccionar una imagen como principal.");return false;
				}
			}
			this.modalForm.confirmLoading = true;
			
			var params = {
				...this.formCrud,
				detalleImg : this.detalleImg.data,
				documentos_remove : this.documentos_remove,
			};

			if (this.formCrud.id == null) {
			axios
				.post("/api/productos/create", params)
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
				.post("/api/productos/update", params)
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
		downloadFile() {
			let params = {
				imagen : this.formCrud.imagen_tranferencia,
				id: this.formCrud.id,
			}
			this.modalForm.dataLoading = true;
			axios
			.post("/api/productos/descargar",params)
			.then((response) => {
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
		},
		removeImage(obj) {
			// console.log(obj,'obj')
			this.documentos_remove.push(obj);
			var idx = this.detalleImg.data.findIndex(x => x.url == obj.url);
			this.detalleImg.data.splice(idx, 1);
		},
		loadImage(){
			this.disabled.upload = false;
			this.modalFormImg.visible = true;
			this.modalFormImg.dataLoading = true;
		},
		handleOkImage(){
			var imagenes = this.documentos;
			// this.detalleImg.data = imagenes.map((res) => ({
			// 	name    : res.name,
			// 	size    : res.size,
			// 	nombre_archivo  : res.nombre_archivo,
			// 	nombre_original : res.nombre_original,
			// 	url_imagen		: res.url_imagen,
			// 	id				: null,
			// 	principal 		: false,
			// }));
			imagenes.forEach((r, i) => {
				let row = {};
				row.id = null,
				row.principal = r.principal;
				row.url = r.nombre_archivo;
				row.nombre_archivo = r.nombre_archivo;
				row.url_imagen = r.url_imagen;
				row.nombre_original = r.nombre_original;
				this.detalleImg.data.push(row);
			});
			this.documentos = [];
			this.modalFormImg.visible = false;

		},
		handleCancelImage(e){
			this.modalFormImg.visible = false;
		},
		handleChangeArchivo: function(info) {
			// console.log(info, 'infor')
			const status = info.file.status;
			if(info.fileList.length > 0){
				info.fileList.forEach(function(r,i){
					if(r.status === 'removed'){
						info.fileList.splice(i,1)  ;
					}
				});
			}
			if (status === 'uploading') {
				/* Puedo manipular el porcentaje de carga */
			}

			if (status === 'done')
			{
				if(info.file.response.success)
				{
					let res = info.file;
					let file = {
						name    : res.name,
						size    : res.size,
						nombre_archivo  : res.response.data[0],
						nombre_original  : res.response.nombre,
						url_imagen		: res.response.url_path,
						principal : (this.detalleImg.data == 0 && this.documentos.length == 0) ? true : false,
					};
					// console.log(file,'file')
					this.documentos.push(file);
					// console.log(this.documentos, 'success');
					this.$message.success(`${info.file.name} archivo subido con éxito.`);
				}else{
					this.$message.error(info.file.response.error);
				}
				this.loadingDropZoneDocumentos--;
			}
			else if (status === 'error')
			{
				this.$message.error(`${info.file.name} fallo al subir archivo.`);
				this.loadingDropZoneDocumentos--;

			} else if (status === 'removed'){
				console.log("Documento eliminado");
			}
			this.disabled.upload = false;
			if(this.documentos.length > 0){
				this.disabled.upload = true;
			}
		},
		beforeUpload: function(file)
		{
			// console.log("before => ", file);
			var isJpgOrPng = file.type === 'image/jpeg' || file.type === 'image/png';
			if (!isJpgOrPng) {
				this.$message.error('Solo puede cargar archivo (JPG, PNG)');
				return false;
			}
			this.loadingDropZoneDocumentos++;
		},
        eliminarDocumentoDropzoneSubidaDocumentos: function(i){
            this.documentos.splice(i, 1);
			
			this.disabled.upload = false;
			if(this.documentos.length > 0){
				this.disabled.upload = true;
			}
        },
		wrapText: function(text, size = 26)
        {
            let re = /(?:\.([^.]+))?$/;
            let ext = re.exec(text);
            let file;

            if(ext !== undefined)
            {
                let base = text.slice(0, (text.length - ext[0].length));

                if(base.length > size)
                {
                    file = base.substring(0, (size - 2)) + '...' + ext[1];
                }else{
                    file = text;
                }

            }
            return file;
        },
		onChangePrincipal: function(obj){
			// var idx = this.detalleImg.data.findIndex(x => x.imagen == obj.imagen);
			if(this.detalleImg.data.length > 1){
				this.detalleImg.data.forEach((r, i) =>{
					// console.log(r.url, 'url')
					r.principal =  (r.url != obj.url) ? false : true;
				});
			}
			// this.detalleImg.data[idx].principal = true;
		},
		isNumber(event, message) {
		if (!/\d/.test(event.key) &&  
			(event.key !== "." || /\./.test(message))  
		)  
			return event.preventDefault();  
		},
	},
};
</script>
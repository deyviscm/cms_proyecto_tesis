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
		  <a-card title="Categorias" style="width: 100%">
			<a-button slot="extra" type="primary" @click="create">Crear</a-button>
			
			<a-row :gutter="24">
				<form @submit.prevent="searchList">
					<a-col :md="4">
						<div class="form-v">
							<label>Nombre</label>
							<a-input v-model="filter.categoria"></a-input>
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
					<a target="_blank" :href="options.url_web+'/productos/'+obj.url">Url Link</a>
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
		width="500px"
		size="lg"
		:dialog-style="{ top: '20px' }"
		class="md-modal"
		>
			<a-spin :spinning="modalForm.dataLoading">
				<a-row :gutter="24">
					<a-row :gutter="16">
						<a-col :sm="24" :md="24" :lg="24" class="mb-2">
							<a-col :md="12">
								<div class="form-v">
									<label>Categoria</label>
									<a-input v-model="formCrud.nombre"></a-input>
								</div>
							</a-col>
							<!-- <a-col :md="12">
								<div class="form-v">
									<label>URL</label>
									<a-input v-model="formCrud.url" :readOnly="true"></a-input>
								</div>
							</a-col> -->
							<!-- <a-col :md="6">
								<div class="form-v">
									<label>Imagen</label>
									<a-input v-model="formCrud.imagen" :readOnly="true"></a-input>
								</div>
							</a-col> -->
						</a-col>
						<a-col :sm="24" :md="24" :lg="24">
							<a-col :sm="24" :md="24" :lg="24" class="mb-2">
								<a-button type="primary" @click="loadImage()" :disabled="detalleImg.data.length>0">Agregar Imagen</a-button>
							</a-col>
							<a-col :sm="24" :md="24" :lg="24" class="mb-2">
								<a-table 
								:columns="detalleImg.columns" 
								:data-source="detalleImg.data"
								size="small">
									<span slot="str_url_imagen" slot-scope="obj">
										<img width="70" :src="obj.url_imagen">
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
							:multiple="false"
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
			categoria : '',
		},
		dataTable: {
			data: [],
			pagination: {},
			sorter: {},
			loading: false,
			columns: [
					{
						title: "Nombre",
						dataIndex: "nombre",
						// sorter: true,
					},
					{
						title: "URL",
						// dataIndex: "url",
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
			nombre: "",
			imagen: "",
			url: "",
			url_imagen: '',
		},
		detalleImg:{
			data : [],
			columns:[
				{
					title: "Imagen",
					scopedSlots: { customRender: "str_url_imagen" },
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
		loadingDropZoneDocumentos : 0,
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
			.get("/api/categorias/form")
			.then((response) => {
				this.options.perfiles = response.data.data.perfiles;
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
			};
			tbl.loading = true;
			params = { results: tbl.pagination.pageSize, ...params, ...filter};
			axios
			.get("/api/categorias", { params })
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
			this.formCrud.nombre = '';
			this.formCrud.url = '';
			this.formCrud.imagen = '';
			this.formCrud.url_imagen = '';
			this.detalleImg.data = [];
		},
		create() {
			this.resetForm();
			this.modalForm.visible = true;
		},
		remove(id) {
			axios
			.post("/api/categorias/remove", { id: id })
			.then((response) => {
				console.log(response,'response')
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
			.get("/api/categorias/info/" + id)
			.then((response) => {
				this.formCrud = response.data.data;
				this.detalleImg.data.push(this.formCrud)
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
				.post("/api/categorias/create", this.formCrud)
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
				.post("/api/categorias/update", this.formCrud)
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
			.post("/api/categorias/descargar",params)
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
			this.formCrud.imagen = "";
			this.formCrud.nombre_archivo = null;
			var idx = this.detalleImg.data.findIndex(x => x.imagen == obj.imagen);
			this.detalleImg.data.splice(idx, 1);
		},
		loadImage(){
			this.disabled.upload = false;
			this.modalFormImg.visible = true;
			this.modalFormImg.dataLoading = true;
		},
		handleOkImage(){
			var imagenes = this.documentos;
			this.detalleImg.data = imagenes.map((res) => ({
				name    : res.name,
				size    : res.size,
				nombre_archivo  : res.nombre_archivo,
				nombre_original : res.nombre_original,
				url_imagen		: res.url_imagen,
			}));

			// cambiar nombre de imagen
			this.formCrud.imagen = this.detalleImg.data[0].nombre_original;
			this.formCrud.nombre_archivo = this.detalleImg.data[0].nombre_archivo;
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
	},
};
</script>
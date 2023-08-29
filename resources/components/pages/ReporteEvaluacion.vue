<template>
  <div>
    <a-row>
      <a-col :span="24">
        <a-card title="Evaluaciones" style="margin-bottom: 10px">
          <a-row :gutter="24">
            <a-col :md="12">
              <div class="form-v">
                <label>ELIJA PROTOCOLO</label>
                <a-select default-value="1" style="width:100%">
                  <a-select-option value="1">Protocolo A - Mensual - Sedes</a-select-option>
                  <a-select-option value="2">Protocolo B - Mensual - Sedes</a-select-option>
                  <a-select-option value="3">Protocolo C - Mensual - Sedes</a-select-option>
                </a-select>
              </div>            
            </a-col>

            <a-col :md="12">
              <div class="form-v">
                <label>ELIJA RANGO DE FECHAS</label>
                <a-range-picker
                  :default-value="[moment('27/02/2021', dateFormatList[0]), moment('27/02/2021', dateFormatList[0])]"
                  :format="dateFormatList"
                  style="width:100%"
                />
              </div>            
            </a-col>
          </a-row>
          <a-col :md="24">
            <hr>
          </a-col>
          <a-col :span="24" class="panel-chart">
            <div class="panel-chart-body">
                <a-table :columns="columns" :data-source="data">
                    <span slot="action-video">
                      <a-button type="danger" @click="viewVideo()">Ver Video</a-button>    
                    </span>
                    <span slot="action-pdf">
                      <a-button type="primary" @click="viewPdf()">Ver PDF</a-button>    
                    </span>
                </a-table>
            </div>            
          </a-col>
        </a-card>
      </a-col>
    </a-row>

    <a-modal
      :title="'VIDEO'"
      :visible="modalVideo.visible"
      :confirm-loading="confirmLoading"
      :ok-button-props= "{ style: { display: 'none'  } }"
      cancel-text="Cerrar"
      @cancel="handleCancel"
      :destroyOnClose="true"
      width="600px"
      size="lg"
    >
      <a-row :gutter="24">
        <a-col :md="24">
          <iframe width="560" height="315" src="https://www.youtube.com/embed/gbQXqYsocYk" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </a-col>
      </a-row>
    </a-modal>
  </div>
</template>

<script>

import moment from 'moment';


const columns = [
  {
    title: 'Fecha',
    dataIndex: 'fecha',
    key: 'fecha',
  },
  {
    title: 'Hora',
    dataIndex: 'hora',
    key: 'hora',
  },
  {
    title: 'Entidad',
    dataIndex: 'entidad',
    key: 'entidad',
    ellipsis: true,
  },
  {
    title: 'Video',
    dataIndex: 'video',
    key: 'video',
    ellipsis: true,
    scopedSlots: { customRender: "action-video" },
  },
  {
    title: 'Pdf',
    dataIndex: 'pdf',
    key: 'pdf',
    ellipsis: true,
    scopedSlots: { customRender: "action-pdf" },
  },
];

const data = [
  {
    key: '1',
    fecha: '12/02/2021',
    hora: '10:00 am',
    entidad: 'Entidad 1',
    video: 'video',
    pdf: 'pdf',
  },
  {
    key: '2',
    fecha: '12/02/2021',
    hora: '10:30 am',
    entidad: 'Entidad 2',
    video: 'video',
    pdf: 'pdf',
  }
];

export default {
  name: "Home",
  data: () => ({
    data,
    columns,
    dateFormat: 'YYYY/MM/DD',
    monthFormat: 'YYYY/MM',
    dateFormatList: ['DD/MM/YYYY', 'DD/MM/YY'],
    modalVideo: {
      visible: false
    },
    confirmLoading: false,
  }),
  components: { 
  },
  mounted: function () {},
  methods: {
    moment,

    viewVideo() {
      this.modalVideo.visible = true;
    },

    viewPdf() {
      window.open('https://eldisenodetinmarin.files.wordpress.com/2016/12/plan-estratc3a9gico.pdf', '_blank');
    },

    handleCancel(e) {
      this.modalVideo.visible = false;
    },
  },

  
};
</script>
<template>
  <div>
    <a-row>
      <a-col :span="24">
        <a-card title="Entidad" style="margin-bottom: 10px">
          <a-row :gutter="24">
            <a-col :md="12">
              <div class="form-v">
                <label>ELIJA PROTOCOLO</label>
                <a-select default-value="1" style="width: 100%">
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
              <highcharts :options="chartOptions1"></highcharts>
            </div>            
          </a-col>
          <a-col :md="8" class="panel-chart">
            <div class="panel-chart-body">
              <highcharts :options="chartOptions2"></highcharts>
              </div>
          </a-col>
          <a-col :md="8" class="panel-chart">
            <div class="panel-chart-body">
              <highcharts :options="chartOptions3"></highcharts>
              </div>
          </a-col>
          <a-col :md="8" class="panel-chart">
            <div class="panel-chart-body">
              <highcharts :options="chartOptions4"></highcharts>
              </div>
          </a-col>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import {Chart} from 'highcharts-vue';
import moment from 'moment';

export default {
  name: "Home",
  data: () => ({
    chartOptions1: {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Reporte General'
      },
      xAxis: {
        type: 'category'
      },
      yAxis: {
        title: {
            text: 'Valoracion'
        }
      },
      series: [{
        name: 'Sedes',
        colorByPoint: true,
        data: [{
            name: 'Los Olivos',
            y: 56.33,
        }, {
            name: 'Comas',
            y: 24.03,
        }, {
            name: 'SMP',
            y: 10.38
        }]
      }],
      credits: false
    },

    chartOptions2: {
      chart: {
        type: 'bar'
      },
      title: {
        text: 'Limpieza'
      },
      xAxis: {
        categories: ['SMP', 'Comas', 'Los Olivos'],
        title: {
            text: null
        }
      },
      yAxis: {
        title: {
            text: 'Valoracion'
        }
      },
      series: [{
        name: '',
        colorByPoint: true,
        data: [{
            name: 'Los Olivos',
            y: 56.33,
        }, {
            name: 'Comas',
            y: 24.03,
        }, {
            name: 'SMP',
            y: 10.38
        }]
      }],
      credits: false
    },

    chartOptions3: {
      chart: {
        type: 'bar'
      },
      title: {
        text: 'Tienda'
      },
      xAxis: {
        categories: ['SMP', 'Comas', 'Los Olivos'],
        title: {
            text: null
        }
      },
      yAxis: {
        title: {
            text: 'Valoracion'
        }
      },
      series: [{
        name: '',
        colorByPoint: true,
        data: [{
            name: 'Los Olivos',
            y: 56.33,
        }, {
            name: 'Comas',
            y: 24.03,
        }, {
            name: 'SMP',
            y: 10.38
        }]
      }],
      credits: false
    },

    chartOptions4: {
      chart: {
        type: 'bar'
      },
      title: {
        text: 'Producto'
      },
      xAxis: {
        categories: ['SMP', 'Comas', 'Los Olivos'],
        title: {
            text: null
        }
      },
      yAxis: {
        title: {
            text: 'Valoracion'
        }
      },
      series: [{
        name: '',
        colorByPoint: true,
        data: [{
            name: 'Los Olivos',
            y: 56.33,
        }, {
            name: 'Comas',
            y: 24.03,
        }, {
            name: 'SMP',
            y: 10.38
        }]
      }],
      credits: false
    },



    dateFormat: 'YYYY/MM/DD',
    monthFormat: 'YYYY/MM',
    dateFormatList: ['DD/MM/YYYY', 'DD/MM/YY'],
  }),
  components: {
    highcharts: Chart 
  },
  mounted: function () {},
  methods: {
    moment,
  },
};
</script>
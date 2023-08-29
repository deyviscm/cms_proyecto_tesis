<template>
  <div>
    <a-row>
      <a-col :span="24">
        <a-card title="Dashboard" style="margin-bottom: 10px">
          <a-row :gutter="24">
            <!-- <a-col :md="12">
              <div class="form-v">
                <label></label>
                <a-select default-value="1" style="width:100%">
                  <a-select-option value="1">Protocolo A - Mensual - Sedes</a-select-option>
                  <a-select-option value="2">Protocolo B - Mensual - Sedes</a-select-option>
                  <a-select-option value="3">Protocolo C - Mensual - Sedes</a-select-option>
                </a-select>
              </div>            
            </a-col> -->
            <a-col :md="12">
              <div class="form-v">
                <label>ELIJA RANGO DE FECHAS</label>
                <a-range-picker
                  v-model="gr1.selectedRange"
                  :default-value="[moment().subtract(2, 'week'), moment()]"
                  :format="dateFormatList" 
                  style="width:100%"
                  @change="handleRangeChange1"
                  
                />
              </div>            
            </a-col>
            <a-col :md="3">
              <a-button type="primary" @click="loadGraficaVentas" style="margin-top: 22px;">Actualizar</a-button>
            </a-col>
            <a-col :md="24" class="panel-chart">
              <div class="panel-chart-body">
                <highcharts :options="chartOptions1" ref="chart1"></highcharts>
              </div>            
            </a-col>
          </a-row>

          <a-row :gutter="24">
            <a-col :md="12">
              <a-card>
                <a-col :md="15">
                  <div class="form-v">
                    <label>ELIJA RANGO DE MESES</label>
                    <a-range-picker
                      :placeholder="['Start month', 'End month']"
                      format="YYYY-MM"
                      @panelChange="handlePanelChange2"
                      @change="handleRangeChange2"
                      :mode="modeMonth"
                      :value="valueMonth"
                    />
                  </div>            
                </a-col>
                <a-col :md="6">
                  <a-button type="primary" @click="loadGraficaVentasMensual" style="margin-top: 22px;">Actualizar</a-button>
                </a-col>
                <a-col :md="24" class="panel-chart">
                  <div class="panel-chart-body">
                    <highcharts :options="chartOptions2" ref="chart2"></highcharts>
                  </div>            
                </a-col>
              </a-card>
            </a-col>
            <a-col :md="12">
              <a-card>
                <a-col :md="15">
                  <div class="form-v">
                    <label>ELIJA RANGO DE AÑOS</label>
                    <a-range-picker
                      :placeholder="['Start year', 'End year']"
                      format="YYYY"
                      @panelChange="handlePanelChange3"
                      @change="handleRangeChange3"
                      :mode="modeYear"
                      :value="valueYear"
                    />
                  </div>            
                </a-col>
                <a-col :md="6">
                  <a-button type="primary" @click="loadGraficaVentasAnios" style="margin-top: 22px;">Actualizar</a-button>
                </a-col>
                <a-col :md="24" class="panel-chart">
                  <div class="panel-chart-body">
                    <highcharts :options="chartOptions3" ref="chart3"></highcharts>
                  </div>            
                </a-col>
              </a-card>
            </a-col>

          </a-row>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>
<style>
.panel-chart-body{
  width: 100%;
  height: 400px;
}
</style>
<script>
import {Chart} from 'highcharts-vue';
import Highcharts from 'highcharts';
import More from 'highcharts/highcharts-more';
import moment from 'moment';

More(Highcharts);

export default {
  name: "HomeCliente",
  data: () => ({
    selectedRange1: null,
    modeMonth:  ['month', 'month'],
    valueMonth: [moment().startOf('year'), moment().endOf('year')],
    modeYear:  ['year', 'year'],
    valueYear: [moment().subtract(1 ,'year'), moment()],
    gr1: {
      desde: moment().subtract(2, 'week').format('YYYY-MM-DD'),
      hasta: moment().format('YYYY-MM-DD'),
    },
    chartOptions1: {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Reporte por Días'
      },
      xAxis: {
        type: 'Fecha'
      },
      yAxis: {
        title: {
            text: 'Total'
        }
      },
      series: [{
        name: 'Fecha',
        colorByPoint: true,
        data: []
      }],
      credits: false
    },

    chartOptions2: {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Reporte por Meses'
      },
      xAxis: {
        categories:[],
        type: 'Mes'
      },
      yAxis: {
        title: {
            text: 'Total'
        }
      },
      series: [{
        name: 'Meses',
        colorByPoint: true,
        data: []
      }],
      credits: false
    },
    chartOptions3: {
      chart: {
        type: 'column'
      },
      title: {
        text: 'Reporte por Años'
      },
      xAxis: {
        categories:[],
        type: 'Año'
      },
      yAxis: {
        title: {
            text: 'Total'
        }
      },
      series: [{
        name: 'Años',
        colorByPoint: true,
        data: []
      }],
      credits: false
    },
    dateFormat: 'YYYY/MM/DD',
    monthFormat: 'YYYY/MM',
    dateFormatList: ['DD/MM/YYYY', 'DD/MM/YY'],
    loading: true
  }),
  components: {
    highcharts: Chart 
  },
  mounted: function () {
    // this.redraw();
    this.loadGraficaVentas();
    this.loadGraficaVentasMensual();
    this.loadGraficaVentasAnios();
  },
  methods: {
    moment,
    redraw:function(){
      // setTimeout(() => {
      //   this.loading = false;
      // }, 600);

      // setTimeout(() => {
      //   this.$refs.chart1.chart.reflow();
      //   this.$refs.chart2.chart.reflow();
      //   this.$refs.chart3.chart.reflow();
      // }, 600);
    },
    handleRangeChange1: function(date, dateString){
      this.gr1.desde = (date[0] != undefined ) ? date[0].format('YYYY-MM-DD') : null;
      this.gr1.hasta = (date[1] != undefined ) ? date[1].format('YYYY-MM-DD') : null;
    },
    loadGraficaVentas: function(){
      axios
			.post("/api/ordenes-compras/graficas-ventas",this.gr1)
			.then((response) => {
				// console.log(response)
        this.chartOptions1.xAxis.categories = response.data.tipoX;
				this.chartOptions1.series = [
          {
            name: 'Fecha',
            colorByPoint: true,
            data: response.data.data
          }
        ]
			})
			.catch((e) => AxiosGlobalError.error(e))
			.finally(() => {
				// this.modalForm.dataLoading = false;
			});
    },
    handleRangeChange2: function(date, dateString){
      console.log(date,'date')
    },
    handlePanelChange2(value, mode) {
      this.valueMonth = value;
      this.modeMonth = [mode[0] === 'date' ? 'month' : mode[0], mode[1] === 'date' ? 'month' : mode[1]];
    },
    loadGraficaVentasMensual: function(){
      axios
			.post("/api/ordenes-compras/graficas-ventas-mensual",{'desde':this.valueMonth[0].format('YYYY-MM-DD'), 'hasta':this.valueMonth[1].format('YYYY-MM-DD')})
			.then((response) => {
				// console.log(response)
        this.chartOptions2.xAxis.categories = response.data.tipoX;
				this.chartOptions2.series = [
          {
            name: 'Meses',
            colorByPoint: true,
            data: response.data.data
        
          }
        ];

			})
			.catch((e) => AxiosGlobalError.error(e))
			.finally(() => {
				// this.modalForm.dataLoading = false;
			});
    },
    handleRangeChange3: function(date, dateString){
      console.log(date,'date')
    },
    handlePanelChange3(value, mode) {
      this.valueYear = value;
      // this.modeYear = [mode[0] === 'date' ? 'year' : mode[0], mode[1] === 'date' ? 'year' : mode[1]];
    },
    loadGraficaVentasAnios: function(){
      axios
			.post("/api/ordenes-compras/graficas-ventas-anios",{'desde':this.valueYear[0].format('YYYY-MM-DD'), 'hasta':this.valueYear[1].format('YYYY-MM-DD')})
			.then((response) => {
				// console.log(response)
        this.chartOptions3.xAxis.categories = response.data.tipoX;
				this.chartOptions3.series = [
          {
            name: 'Año',
            colorByPoint: true,
            data: response.data.data
        
          }
        ];

			})
			.catch((e) => AxiosGlobalError.error(e))
			.finally(() => {
				// this.modalForm.dataLoading = false;
			});
    },
  },
};
</script>
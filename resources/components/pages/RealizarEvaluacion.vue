<template>
  <div>
    <!-- <div style="height:200px;overflow:auto">
    <pre>
      {{preguntas}}
    </pre>
    </div>
    <pre>
    {{respuestas}}
    </pre>
    requeridas {{respuestasValidas()}} -->
    <a-row>
      <a-col :span="24">
        <a-card
          title="FORMULARIO"
          style="margin-bottom: 10px"
        >
            <div v-if="!encuestaInvalida">
              <div>
                Buenos días/tardes, mi nombre es ___________________, de la empresa encuestadora Incognitoslab. Estamos realizando encuestas que tienen como objetivo hacer una evaluación del servicio que brinda la empresa que nos ha contratado, para conocer su opinión. Con su respuesta y la de otros clientes, esta empresa podrá mejorar la calidad de los servicios que ofrece a sus clientes.<br>
                El llenado de la encuesta es voluntario. Incognitoslab. protegerá toda la información que usted brinda libremente, la cual incluye datos personales, y nos permitirá realizar la presente investigación, por lo que nos brinda su consentimiento por tiempo indefinido para tal efecto. Esta será utilizada únicamente para cumplir con esta tarea y la almacenaremos en nuestro banco de datos de encuestados. En casos puntuales, su información personal podrá ser compartida con el interesado en este estudio. Para cualquier inquietud sobre sus datos personales, envíe una comunicación a administracion@incognitoslab.com<br>
                ¿Está usted de acuerdo?    1 Sí (continuar)      2.No (terminar)<br>
                Antes de iniciar, le informo que las encuestas son por encargo de la empresa de servicio eléctrico Enel.
              </div>
                <div v-for="item in preguntas" v-bind:key="item.id" >
                  <div v-if="!(item.pregunta_activadora_cond != null && !validIfRequired(item.pregunta_activadora_cond))">
                    <div style="margin-bottom: 10px">
                      <b>{{item.codigo_externo}} - {{item.nombre}}</b>
                      <div v-if="item.tipo_respuesta == 'TEXTO'">
                          <div v-if="item.identificador">
                            <a-input placeholder="" v-model="respuestas[item.id]" :maxLength="item.longitud"
                            @keyup='validarUnico(respuestas[item.id])'
                            :validate-status="(identificadorUnicoValidado == true && identificadorUnicoValidodoRespuesta == false ? 'error':'')"
                            />
                            <div v-if="identificadorUnicoValidado == true && identificadorUnicoValidodoRespuesta == true">
                              <a-tag style="margin-top: 5px" color="green">El dato es válido.</a-tag>
                            </div>
                            <div v-if="identificadorUnicoValidado == true && identificadorUnicoValidodoRespuesta == false">
                              <a-tag style="margin-top: 5px" color="red">El dato ya existe en otra encuesta.</a-tag>
                            </div>
                          </div>
                          <div v-else>
                            <a-input placeholder="" v-model="respuestas[item.id]" :maxLength="item.longitud" />
                          </div>
                          <div v-if="item.longitud">
                            El campo debe tener {{item.longitud}} digitos.
                          </div>
                      </div>
                      <div v-if="item.tipo_respuesta == 'KEYWORD'">
                          <a-input placeholder="" v-model="respuestas[item.id]"  />
                      </div>
                      <div v-if="item.tipo_respuesta == 'UNICA_DEPENDIENTE'">
                        <a-radio-group v-decorator="['radio-group']" v-model="respuestas[item.id]" @change="changeOption(item.id);">
                          <a-radio :value="k+'=>'+o" v-for="(o,k) in listaDependiente(item.respuesta_valores, item.pregunta_activadora_cond)" v-bind:key="k" @change="finalizar(item.respuesta_finalizadora, k)">{{o}}</a-radio>
                        </a-radio-group>
                      </div>
                      <div v-if="item.tipo_respuesta == 'UNICA'">
                        <a-radio-group v-decorator="['radio-group']" v-model="respuestas[item.id]" @change="changeOption(item.id);">
                          <a-radio :value="k+'=>'+o" v-for="(o,k) in item.respuesta_valores" v-bind:key="k" @change="finalizar(item.respuesta_finalizadora, k)">{{o}}</a-radio>
                        </a-radio-group>
                      </div>
                      <div v-if="item.tipo_respuesta == 'MULTIPLE'">
                        <a-checkbox-group v-decorator="['checkbox-group']" v-model="respuestas[item.id]" @change="changeOption(item.id);">
                          <a-checkbox :value="k+'=>'+o" v-for="(o,k) in item.respuesta_valores" v-bind:key="k" @change="finalizar(item.respuesta_finalizadora, k)">{{o}}</a-checkbox>
                        </a-checkbox-group>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else>
                <a-alert message="El encuestado no cumple con los requisitos. Agradecer y finalizar." banner />
              </div>
              <a-form-item class="text-center">
                <a-button type="primary" @click="goHome()" v-if="encuestaInvalida">Regresar</a-button
                >
                <a-button type="primary" :disabled="!validButton() || loadingButton" @click="handleOk" v-if="!encuestaInvalida"> Enviar respuestas </a-button>
              </a-form-item>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import AxiosGlobalError from "../../services/AxiosGlobalError";
import moment from "moment";

export default {
  name: "Home",
  data: () => ({
    preguntas: [],
    respuestas: {},
    encuestaInvalida: false,
    identificadorUnico: false,
    identificadorUnicoValidado: false,
    identificadorUnicoValidodoRespuesta: false,
    id: 3,
    typing: null,
    loadingButton: false
  }),
  components: {},
  mounted: function () {
    this.init();
  },
  methods: {
    init(){
      // console.log("ejecutado");
      axios
        .get("/api/encuesta/"+this.id)
        .then((response) => {
          this.preguntas = response.data.data;
          this.preguntas.forEach(element => {
            if(element.identificador == 1) this.identificadorUnico = true;
          });
        })
        .catch((e) => AxiosGlobalError.error(e))
        .finally(() => {});
    },
    goHome() {
      this.$router.push("/home");
    },
    validButton(){
      let response = false;
      let data = this.respuestasValidas();

      if(this.identificadorUnico){
        if(!this.identificadorUnicoValidodoRespuesta){
          return false;
        }else{
          return data.formValidado;
        }
      } else {
        return data.formValidado;
      }
      
    },
    handleOk(){
        let data = this.respuestasValidas();
        this.loadingButton = true;
        axios
          .post("/api/encuesta/create", {
            encuesta_id: this.id,
            encuesta: data.respuestasValidas
          })
          .then((response) => {
            // this.fetch({
            //   pageSize: this.dataTable.pagination.pageSize,
            //   page: this.dataTable.pagination.current,
            //   sortField: this.dataTable.sorter.field,
            //   sortOrder: this.dataTable.sorter.order,
            // });
            this.$message.success("Datos registrados");
            // this.modalForm.visible = false;
            this.$router.push("/home");
          })
          .catch((e) => AxiosGlobalError.error(e))
          .finally(() => {
            // this.modalForm.confirmLoading = false;
            this.loadingButton = false;
          });
    },
    respuestasValidas(){
      let listaRequeridas = [];
      let listaDescartadas = [];
      let respuestasValidas = {};
      // let contadorValidas = 0;

      for (let i = 0; i < this.preguntas.length; i++) {
        if(this.preguntas[i].pregunta_activadora_cond != null && !this.validIfRequired(this.preguntas[i].pregunta_activadora_cond)){
          listaDescartadas.push(this.preguntas[i].id);
        } else {
          listaRequeridas.push(this.preguntas[i].id);
        }
      }
      
      // console.log("text");
      // console.log(listaRequeridas);

      Object.entries(this.respuestas).forEach(([k, v]) => {
        var longitud = null;
        for (let i = 0; i < this.preguntas.length; i++) {
          // const element = this.preguntas[i];
          if(k == this.preguntas[i].id){
            longitud = this.preguntas[i].longitud;
          }
        }

        if((v && longitud === null) || (v.length == longitud)){
          for (let i = 0; i < listaRequeridas.length; i++) {
            if(k == listaRequeridas[i]){
              respuestasValidas[k] = v;
            }
          }
        }
      });

      let data = {
        requeridas: listaRequeridas,
        descartadas: listaDescartadas,
        respuestasValidas: respuestasValidas,
        formValidado: Object.keys(respuestasValidas).length == listaRequeridas.length,
        // contadorValidas: contadorValidas,
        // listaRequeridas: listaRequeridas.length
      };
      
      return data;
    },
    listaDependiente(valores, pregunta_activadora){
      let keyPregunta = null;
      let keyRespuesta = null;
      let list = [];

      Object.entries(pregunta_activadora).forEach(([k, v]) => {
        keyPregunta = k;
      });

      Object.entries(this.respuestas).forEach(([k, v]) => {
        if(k == keyPregunta){
          
          if(!Array.isArray(v)){
            var porciones = (v).split('=>');
            if(porciones.length > 1){
              keyRespuesta = porciones[0];
            }
          }
        }
        // if(v){
          // for (let i = 0; i < listaRequeridas.length; i++) {
          //   if(k == listaRequeridas[i]){
          //     respuestasValidas[k] = v;
          //   }
          // }
        // }
      });
      
      // console.log("keyPregunta: "+keyPregunta);

      for (const [key, value] of Object.entries(valores)) {
        console.log(key, value);
        if(key == keyRespuesta){
          list = value;
        }
      }

      // for (let i = 0; i < valores.length; i++) {
      //   const element = valores[i];
      //   if
      // }

      return list;
    },
    finalizar(respuesta_finalizadora, respuesta_actual){
      if(respuesta_finalizadora == respuesta_actual){
        this.encuestaInvalida = true;
      }
    },
    changeOption(id){
      // console.log('opcion cambiada');
      
      if(id){
        for (let i = 0; i < this.preguntas.length; i++) {
          if(this.preguntas[i].pregunta_activadora_cond != null){
            let k = Object.keys(this.preguntas[i].pregunta_activadora_cond)[0];
            if(k == id){
              delete this.respuestas[this.preguntas[i].id]; 
            }
          }
        }
      }
    },
    validarUnico(value){
      if (this.typing) {
        clearTimeout(this.typing);
      }

      this.identificadorUnicoValidado = false;
      this.identificadorUnicoValidodoRespuesta = false;

      this.typing = setTimeout( () => {
        axios
          .get("/api/encuesta/validate-entity/"+this.id+"?id="+value)
          .then((response) => {
            if(response.data.valid){
              this.identificadorUnicoValidodoRespuesta = true;
            }
          })
          .catch((e) => {AxiosGlobalError.error(e); console.log('cancelado');})
          .finally(() => {this.identificadorUnicoValidado = true;});
      }, 800);
    },
    validIfRequired(condition){
      let preguntas = false;
      let kPregunta = null;
      let vPregunta = [];

      Object.entries(condition).forEach(([key, value]) => {
          kPregunta = key;
          Object.entries(value).forEach(([k, v]) => {
            // console.log(key+' '+v);
            vPregunta.push(v);
          });
      });

      Object.entries(this.respuestas).forEach(([key, value]) => {
        // console.log("pkey"+key);
        // console.log("p"+kPregunta);
        if(key == kPregunta){
          // console.log("verificar rpta");
          // console.log(value);

          if(Array.isArray(value)){
            
            for (let i = 0; i < value.length; i++) {
              // const element = value[i];
              if(!Array.isArray(value[i])){
                var porciones = (value[i]).split('=>');
                if(porciones.length > 1){
                  // // console.log(porciones[0]); //porción3
                  if(vPregunta.includes(porciones[0])){
                    preguntas = true;
                  }
                }
              }
            }

          }else{
            if(!Array.isArray(value)){
              var porciones = value.split('=>');
              if(porciones.length > 1){
                // // console.log(porciones[0]); //porción3
                if(vPregunta.includes(porciones[0])){
                  preguntas = true;
                }
              }
            }
          }
        }
      });

      return preguntas;
    },
    moment,
  },
};
</script>
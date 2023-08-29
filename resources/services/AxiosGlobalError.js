import { message } from 'ant-design-vue';
import AuthService from './AuthService';
import router from '../js/routes';

class AxiosGlobalError {

  error(e) {
    
    if (e.response) {
      switch (e.response.status) {
        case 401:
          message.error('La sesión ha caducado');
          AuthService.logout();
          router.go('/');
          break;

        case 422:
          if (e.response.data.hasOwnProperty('errors')) {
            let k = Object.keys(e.response.data.errors)[0];
            message.error(e.response.data.errors[k][0]);
          } else {
            message.error('Error de validación');
          }
          break;

        case 500:
          message.error(e.response.data.message);
          break;

        default:
          message.error('Ha ocurrido un error: ' + e.message);
          break;
      }
      // Request made and server responded
      // console.log(e.response.data);
      // console.log(e.response.status);
      // console.log(e.response.headers);
    } else if (e.request) {
      // The request was made but no response was received
      message.error('Ha ocurrido un error: ' + e.request);
    } else {
      // Something happened in setting up the request that triggered an Error
      message.error('Ha ocurrido un error: ' + e.message);
    }
  }

}

export default new AxiosGlobalError();
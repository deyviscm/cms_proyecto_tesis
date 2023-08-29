import axios from 'axios';
import jwt_decode from "jwt-decode";

const API_URL = '';

class AuthService {
  
  login(data) {
    if (data.access_token) {
      localStorage.setItem('jwtAuth', data.access_token);
      this.loadHeaders();
      return true;
    }else{
      return false;
    }
  }
  
  logout() {
    localStorage.removeItem('jwtAuth');
    this.loadHeaders();
  }

  isLogged() {
    let user = localStorage.getItem('jwtAuth');
  
    if (user) {
      return true;
    } else {
      return false;
    }
  }

  loadHeaders(){
    let user = localStorage.getItem('jwtAuth');
    
    if (user) {
      // console.log("JWT");
      // console.log(jwt_decode(user));
      window.axios.defaults.headers.common['Authorization'] = 'bearer '+user;
    } else {
      window.axios.defaults.headers.common['Authorization'] = '';
    }
  }

  profile(){
    let user = localStorage.getItem('jwtAuth');
    
    let profileId = 0;

    if (user) {
      let jwt = jwt_decode(user);
      profileId = jwt.profile;
    }

    return profileId;
  }

}

export default new AuthService();
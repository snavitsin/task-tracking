import Vue from 'vue';
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(VueAxios, axios);

export default {

async post(request_url, params){
  let response = null;
  await Vue.axios.post(`${request_url}`, {params}).then(res => {
    response = res.data;
  })
    .catch(error => {
      console.log(error);
      //this.errored = true;
    });
    //.finally(() => (this.loading = false));

  return response;
}


}
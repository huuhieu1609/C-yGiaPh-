import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Default from './Layout/Wrapper/index.vue'
import Client from './Layout/Wrapper/client.vue'
import Blank from './Layout/Wrapper/blank.vue'
import Partner from './layout/wrapper/partner.vue'
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
const app = createApp(App)

app.use(router)
app.use(Toast, {
  timeout: 3000,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  position: POSITION.TOP_RIGHT
});

app.component("default-layout", Default);
app.component("client-layout", Client);
app.component("blank-layout", Blank);
app.component("partner-layout", Partner);
app.mount("#app")
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import "./assets/main.css";

import BaseCard from "./components/UI/BaseCard.vue";
import BaseButton from "./components/UI/BaseButton.vue";
import DarkModeButton from "./components/UI/DarkModeButton.vue";
import BaseInput from "./components/UI/BaseInput.vue";
import BaseListItem from "./components/UI/BaseListItem.vue";
import BaseDialog from "./components/UI/BaseDialog.vue";
import IconLogo from "./components/icons/IconLogo.vue";

const app = createApp(App);

app.component("base-card", BaseCard);
app.component("base-button", BaseButton);
app.component("darkmode-button", DarkModeButton);
app.component("base-input", BaseInput);
app.component("base-list-item", BaseListItem);
app.component("base-dialog", BaseDialog);
app.component("icon-logo", IconLogo);
app.use(router);
app.use(store);
app.mount("#app");
app.config.unwrapInjectedRef = true;

<template>
  <AccountViewHeader
    :isSideBarActive="isSideBarActive"
    @toggleLeftSideBar="toggleSideBar"
  />
  <div class="wrapper">
    <AccountViewSideBar
      :isSideBarActive="isSideBarActive"
      @closeSideBar="toggleSideBar"
    />
    <RouterView />
  </div>
</template>

<script lang="ts">
import AccountViewHeader from "../../components/AccountView/AccountViewHeader.vue";
import AccountViewSideBar from "../../components/AccountView/AccountViewSideBar.vue";
export default {
  name: "AccountView",
  components: { AccountViewHeader, AccountViewSideBar },
  data() {
    return {
      isSideBarActive: false,
    };
  },
  methods: {
    toggleSideBar(status: boolean) {
      this.isSideBarActive = status;
    },
  },
  computed: {},
  mounted() {},
};
</script>

<style lang="scss" scoped>
$mobile-width: 480px;
$tablet-width: 768px;
.wrapper {
  display: flex;
  width: 80%;
  margin: auto;
  background: var(--background-glass-color-primary);
  height: calc(100vh - 80px);
}

@media (max-width: $tablet-width) {
  .wrapper {
    width: 100%;
  }
}
</style>

<template>
  <v-app-bar
    color="primary"
    dark
    app
    permanent
    clipped-left
    fixed
    flat
    disable-resize-watcher="false"
  >
    <v-app-bar-nav-icon @click="iconClicked" v-if="iconVisible"></v-app-bar-nav-icon>
    <span v-for="item in headers" :key=item.title>
      <v-btn v-if="item.title == selectItem" text :href="item.link" disabled> {{ item.title }} </v-btn>
      <v-btn v-else text :href="item.link"> {{ item.title }} </v-btn>
    </span>
    <v-spacer></v-spacer>

      <v-btn href="/user" text> <v-icon dark>mdi-account-circle</v-icon> </v-btn>
  </v-app-bar>
</template>

<script>
export default {
  props: {
    drawer: Boolean,
    iconVisible: Boolean,
    iconClicked: Function,
    selectItem: String,
  },
  data: function() {
    let headers = [
        {title: 'home', link: '/'},
    ];
    if(localStorage.isAdmin === '1') {
      headers.push({title: 'admin', link: '/admin'});
    }
    headers.push({title: 'logout', link: '/logout'});
    return {
      headers : headers
    }
  },
};
</script>

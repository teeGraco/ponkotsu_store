<template>
  <v-app>
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="10" md="8">
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Login form</v-toolbar-title>
                <v-spacer />
              </v-toolbar>
              <v-card-text>
                <div>
                  <strong class="pink--text">{{ message }}</strong>
                </div>
                <v-form
                @submit.prevent
                >
                  <v-text-field
                    label="name"
                    name="name"
                    prepend-icon="account_box"
                    type="text"
                    v-model="name"
                    @keyup.enter="postLogin"
                  />

                  <v-text-field
                    id="password"
                    label="Password"
                    name="password"
                    prepend-icon="lock"
                    type="password"
                    v-model="password"
                    @keyup.enter="postLogin"
                  />
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn text color="light-blue darken-1" href="/create">新規登録</v-btn>
                <v-btn text color="light-blue darken-1" href="/forget">パスワードを忘れた</v-btn>
                <v-spacer />
                <v-btn @click="postLogin" color="primary">Login</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
      <!-- ログイン中ダイアログ -->
      <v-dialog v-model="waitDialog" hide-overlay persistent width="300">
        <v-card color="primary" dark>
          <v-card-text>
            ログインしています………
            <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-content>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      name: "",
      password: "",
      waitDialog: false,
      message: ""
    };
  },
  methods: {
    postLogin() {
      this.waitDialog = true;
      axios
        .post("/api/login", {
          name: this.name,
          password: this.password
        })
        .then(response => {
          let d = new Date();
          d.setTime(d.getTime() + 7 * 24 * 60 * 60 * 1000); // 7日
          const expires = "expires=" + d.toUTCString();
          document.cookie =
            "session_id=" +
            response.data.session_id +
            ";" +
            expires +
            ";path=/";
          localStorage.isAdmin = response.data.admin;
          window.location.href = window.location.origin + "/";
          this.waitDialog = false;
        })
        .catch(error => {
          this.message = error.response.data.message;
          this.waitDialog = false;
        });
    }
  }
};
</script>
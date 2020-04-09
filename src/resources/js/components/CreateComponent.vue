<template>
  <v-app>
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="10" md="8">
            <v-card class="elevation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title>Create Account</v-toolbar-title>
                <v-spacer />
              </v-toolbar>
              <v-card-text>
                <div>
                  <strong class="pink--text">{{ message }}</strong>
                </div>
                <v-form
                v-model="valid"
                @submit.prevent
                >
                  <v-text-field
                    id="name"
                    label="name"
                    name="name"
                    prepend-icon="account_box"
                    type="text"
                    :rules="nameRules"
                    v-model="name"
                  />
                  <v-text-field
                    label="Email"
                    name="email"
                    prepend-icon="email"
                    type="text"
                    :rules="emailRules"
                    v-model="email"
                  />
                  <v-text-field
                    id="password"
                    label="Password"
                    name="password"
                    prepend-icon="lock"
                    type="password"
                    :rules="passwordRules"
                    v-model="password"
                  />
                  <v-file-input
                  multiple label="アイコン画像(png, jpeg, gif)"
                  @change="onImagePicked"
                  accept="image/png,image/jpeg,image/gif"
                  ></v-file-input>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn text color="light-blue darken-1" href="/login">ログイン</v-btn>
                <v-btn text color="light-blue darken-1" href="/forget">パスワードを忘れた</v-btn>
                <v-spacer />
                <v-btn :disabled="!valid" @click="postLogin" color="primary">ユーザーを作成する</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
      <!-- ユーザー作成中ダイアログ -->
      <v-dialog v-model="waitDialog" hide-overlay persistent width="300">
        <v-card color="primary" dark>
          <v-card-text>
            ユーザーを作成しています…
            <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
          </v-card-text>
        </v-card>
      </v-dialog>
      <!-- ユーザー作成完了ダイアログ -->
      <v-dialog v-model="messageDialog" persistent width="500">
        <v-card class="mx-auto" max-width="500" outlined>
          <v-list-item one-line>
            <v-list-item-content>
              <v-list-item-title class="headline mb-1">ユーザーの作成を完了しました</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-card-actions>
            <v-btn text color="light-blue darken-1" href="/login">ログイン画面</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-content>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      email: "",
      emailRules: [
        v => !!v || 'Emailアドレスは必須です',
        v => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || '有効なEmailアドレスを入力してください',
      ],
      password: "",
      passwordRules: [
        v => !!v || 'パスワードは必須です',
      ],
      name: "",
      nameRules: [
        v => !!v || '名前は必須です',
      ],
      message: "",
      waitDialog: false,
      messageDialog: false,
      icon: null
    };
  },
  methods: {
    postLogin: function() {
      this.waitDialog = true;
      var formData = new FormData();
      formData.append("icon", this.icon);
      formData.append("email", this.email);
      formData.append("password", this.password);
      formData.append("name", this.name);
      axios
        .post("/api/create", formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(response => {
          this.messageDialog = true;
          this.waitDialog = false;
        })
        .catch(error => {
          this.message = error.response.data.message;
          this.waitDialog = false;
        });
    },
    onImagePicked: function(file) {
      if (file.length == 0) {
        this.message = "";
        this.uploadedIcon = null;
        this.icon = null;
        return;
      }
      if (file.length != 1) {
        this.message = "選択できるファイルは1つまでです";
        return;
      }
      this.icon = file[0];
    }
  }
};
</script>
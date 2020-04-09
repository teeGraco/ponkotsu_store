<template>
  <v-app>
    <v-content>
      <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
          <v-col cols="12" sm="10" md="8">
            <v-card class="elvation-12">
              <v-toolbar color="primary" dark flat>
                <v-toolbar-title> {{ title }} </v-toolbar-title>
                <v-spacer />
              </v-toolbar>
              <v-card-text>
                <div>
                  <strong class="pink--text"> {{ errorMessage }} </strong>
                </div>
                <v-form
                v-model="valid"
                @submit.prevent
                >
                  <v-text-field
                    :label="textField.label"
                    v-if="step==0"
                    :rules="emailRules"
                    type="email"
                    v-model="email"
                    @keyup.enter="postForm"
                  />
                  <v-text-field
                    label="token"
                    v-if="step==1"
                    type="text"
                    v-model="token"
                    @keyup.enter="validateToken"
                  />
                  <v-text-field
                    label="password"
                    v-if="step==2"
                    type="password"
                    v-model="password"
                    @keyup.enter="resetPassword"
                  />
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn text color="light-blue darken-1" href="/login">ログイン</v-btn>
                <v-btn text color="light-blue darken-1" href="/create">新規ユーザー作成</v-btn>
                <v-btn text v-if="step==1" color="light-blue darken-1"  @click="step=0"> メールの再送信 </v-btn>
                <v-spacer />
                <v-btn :disabled="!valid" @click="postForm" v-if="step==0" color="primary">メールを送信する</v-btn>
                <v-btn @click="validateToken" v-if="step==1" color="primary">トークンを送信する</v-btn>
                <v-btn @click="resetPassword" v-if="step==2" color="primary">パスワードをリセットする</v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
      <!-- ログイン中ダイアログ -->
      <v-dialog v-model="waitDialog" hide-overlay persistent width="300">
        <v-card color="primary" dark>
          <v-card-text>
            {{ dialogMessage }}
            <v-progress-linear indeterminate color="white"></v-progress-linear>
          </v-card-text>
        </v-card>
      </v-dialog>

      <!-- パスワードリセット完了ダイアログ -->
      <v-dialog v-model="completeDialog" persistent width="500">
        <v-card class="mx-auto" max-width="500" outlined>
          <v-list-item one-line>
            <v-list-item-content>
              <v-list-item-title class="headline mb-1"> パスワードをリセットしました </v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-card-actions>
            <v-btn text color="light-blue darken-1" href="/login"> ログイン画面 </v-btn>
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
      valid: false,
      email: "",
      emailRules: [
        v => !!v || 'Emailアドレスは必須です',
        v => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || '有効なEmailアドレスを入力してください',
      ],
      token: "",
      tokenRules: [
        v => !!v || 'トークンは必須です',
      ],
      password: "",
      waitDialog: false,
      message: "",
      step: 0, //0: メールアドレス入力、1:トークン入力、2:パスワード入力
      dialogMessage: "",
      errorMessage: "",
      completeDialog: false,
    };
  },
  methods: {
    postForm() {
      this.waitDialog = true;
      this.dialogMessage = "トークンを送信しています...";
      axios
        .post("/api/forget/send", {
          email: this.email,
        })
        .then(response => {
          this.step = 1;
        })
        .catch(error => {
          this.waitDialog = false;
          this.errorMessage = error.response.data.message;
        });
    },
    validateToken() {
      this.dialogMessage = "トークンを確認中...";
      this.waitDialog = true;
      axios
        .post("/api/forget/validate", {
          token: this.token,
        })
        .then(response => {
          this.step = 2;
        })
        .catch(error => {
          this.waitDialog = false;
          this.errorMessage = error.response.data.message;
        });
    },
    resetPassword() {
      this.waitDialog = true;
      axios
        .post("/api/forget/resetpass", {
          token: this.token,
          password: this.password,
        })
        .then(response => {
          this.completeDialog = true;
        })
        .catch(error => {
          this.waitDialog = false;
          this.errorMessage = error.response.data.message;
        });
    },
  },
  computed: {
    title: function() {
      const titles = ["登録メールアドレスを入力してください", "メールに記載されている「リセットトークン」を入力してください", "パスワードを入力してください", ];
      return titles[this.step];
    },
    textField: function() {
      return {
          label: "email",
          type: "text",
          model: this.email,
          post: this.postForm
      }
    }
  }, 
  watch: {
    step: function(val) {
      this.waitDialog = false;
      this.errorMessage = "";
      this.token= "";
    }
  }
};
</script>
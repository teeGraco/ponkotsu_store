<template>
  <div class="container">
    <v-app>
      <appbar-component />
      <v-content>
        <v-container fluid>
          <v-row wrap>
            <v-col cols="12" sm="12" md="6" lg="6" xl="6">
              <v-layout justify-center>
              <v-img v-if="thumbnail" width=512 :src="thumbnail"></v-img>
              </v-layout>
            </v-col>
            <v-col cols="12" sm="12" md="6" lg="6" xl="6">
              <v-card class="mx-auto">
                <v-card-text>
                  <p class="display-1 text--primary">{{title}}</p>
                  <p>{{description}}</p>
                  <span style="color: red">{{ price }} pt.</span>
                </v-card-text>
                <v-card-actions class="justify-center">
                  <!-- レビューを書くダイアログを出す -->
                  <v-dialog v-model="dialog" max-width="600px">
                    <template v-slot:activator="{ on }">
                      <v-btn color="primary" dark v-on="on" class="ma-4" large>レビューを書く</v-btn>
                    </template>
                    <v-card>
                      <v-card-title>
                        <span class="headline">レビューを書く</span>
                      </v-card-title>
                      <v-card-text>
                        <v-container>
                          <v-row>
                            <v-col cols="12">
                              <v-list-item two-line>
                                <v-list-item-content>
                                  <v-list-item-title>総合評価</v-list-item-title>
                                  <v-rating v-model="rating"></v-rating>
                                </v-list-item-content>
                              </v-list-item>
                            </v-col>
                            <v-col cols="12">
                              <v-textarea v-model="message" solo label="レビューを記入してください"></v-textarea>
                            </v-col>
                          </v-row>
                        </v-container>
                      </v-card-text>
                      <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="dialog = false">Close</v-btn>
                        <v-btn color="blue darken-1" text @click="submitReview">送信する</v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                  <v-btn large color="primary" :href="buyLink">購入画面へ進む</v-btn>
                </v-card-actions>
              </v-card>

              <div class="mt-12">
                <h3>レビュー</h3>
                <v-card v-for="(review, index) in reviews" :key="index" class="mb-2" outlined>
                  <v-list-item three-line>
                    <v-list-item-content>
                      <div class="overline mb-4">{{ review.name + " " + review.updated_at }}</div>
                      <v-rating v-model="review.rating" :readonly="true"></v-rating>
                      <div v-html="review.message"></div>
                      <v-img v-if="review.ogImageUrl" :src="`data:image;base64,${review.ogImageUrl}`" />
                      <div v-html="review.ogTitle"></div>
                    </v-list-item-content>

                    <v-list-item-avatar tile size="80" color="greymx-auto">
                      <v-img :src="'/api/image?file=users/'+review.icon+'&x=128&y=128'"></v-img>
                    </v-list-item-avatar>
                  </v-list-item>
                </v-card>
              </div>
            </v-col>
          </v-row>

          <!-- 購入中ダイアログ -->
          <v-dialog v-model="postingDialog" hide-overlay persistent width="300">
            <v-card color="primary" dark>
              <v-card-text>
                レビューを投稿しています………
                <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
              </v-card-text>
            </v-card>
          </v-dialog>

          <!-- 購入完了ダイアログ -->
          <v-dialog v-model="completeDialog" width="600">
            <v-card class="mx-auto" max-width="644" outlined>
              <v-list-item>
                <v-list-item-content>
                  <v-list-item-title class="headline mb-1">{{ dialogMessage }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>

              <v-card-actions>
                <v-btn text @click="completeDialog=false">閉じる</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-container>
      </v-content>
    </v-app>
  </div>
</template>

<script>
export default {
  props: {
    goodId: String
  },

  data: function() {
    return {
      title: "",
      description: "",
      ord: "",
      thumbnail: "",
      buyLink: "",
      goods: [],
      price: 0,
      reviews: [],
      dialog: false,
      rating: 3,
      message: "",
      postingDialog: false,
      completeDialog: false,
      dialogMessage: ""
    };
  },

  mounted() {
    this.getGood();
  },

  methods: {
    submitReview: function() {
      this.postingDialog = true;
      axios
        .post("/api/review", {
          good_id: this.goodId,
          message: this.message,
          rating: this.rating
        })
        .then(request => {
          this.dialogMessage = "レビューの投稿に成功しました。";
          this.postingDialog = false;
          this.dialog = false;
          this.completeDialog = true;
          this.message = "";
          this.getGood();
        })
        .catch(error => {
          this.dialogMessage = "レビューの投稿に失敗しました。";
          this.postingDialog = false;
          this.dialog = false;
          this.completeDialog = true;
        });
    },
    getGood: function() {
      axios.get("/api/goods/" + this.goodId).then(request => {
        this.title = request.data.title;
        this.description = request.data.desc;
        this.price = request.data.price;
        this.buyLink = "/buy/" + request.data.id;
        this.thumbnail = "/storage/" + request.data.thumbnail;
        this.reviews = request.data.reviews;
      });
    }
  }
};
</script>

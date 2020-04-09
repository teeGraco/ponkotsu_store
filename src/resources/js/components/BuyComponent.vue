
<template>
  <div class="container">
    <v-app>
      <appbar-component />
      <v-content>
        <v-container fluid>
          <v-row wrap>
            <v-col>
              <v-card>
                <v-list-item two-line>
                  <v-list-item-content>
                    <v-list-item-title class="headline mb-1">購入者: {{ userName }}</v-list-item-title>
                    <v-list-item-title class="headline mb-1">残高: {{ userBalance }} pts.</v-list-item-title>
                  </v-list-item-content>
                  <!-- <v-list-item-avatar tile size="80" color="grey"></v-list-item-avatar> -->
                </v-list-item>
              </v-card>
            </v-col>
          </v-row>
          <v-row wrap>
            <v-col cols="12" sm="12" md="6" lg="6" xl="6">
              <v-card class="mx-auto">
                <v-row class="pa-2 ma-2">
                  <v-col class="pa-2 ma-2">
                    <v-layout justify-center>
                      <v-img :src="thumbnail" width="200px"></v-img>
                    </v-layout>
                  </v-col>
                  <v-col>
                    <h2>{{ title }}</h2>
                    <div>{{ description }}</div>
                    <div>
                      価格
                      <span style="color: red">{{ goodPrice }} pt.</span>
                    </div>
                  </v-col>
                </v-row>
              </v-card>
            </v-col>
            <v-col cols="12" sm="12" md="6" lg="6" xl="6">
              <div>
                <v-select
                  v-model="selectedCoupon"
                  :items="couponDescriptions"
                  item-text="description"
                  item-value="id"
                  label="クーポン(何度でも利用可能)"
                ></v-select>
              </div>
              <div>
                <v-select v-model="selectedCnt" :items="cnts" label="個数"></v-select>
              </div>
              <div>合計金額: {{ total }} pts.</div>
              <v-btn large @click.stop="buy()">購入する</v-btn>
            </v-col>
          </v-row>

          <!-- 購入中ダイアログ -->
          <v-dialog v-model="waitDialog" hide-overlay persistent width="300">
            <v-card color="primary" dark>
              <v-card-text>
                購入しています………
                <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
              </v-card-text>
            </v-card>
          </v-dialog>

          <!-- 購入完了ダイアログ -->
          <v-dialog v-model="completeDialog" persistent width="300">
            <v-card class="mx-auto" max-width="344" outlined>
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-title class="headline mb-1">購入成功</v-list-item-title>
                  <v-list-item-subtitle>{{ title }} を購入しました</v-list-item-subtitle>
                  <v-list-item-subtitle>購入金額: {{ total }}</v-list-item-subtitle>
                  <v-list-item-subtitle>残高: {{ newBalance }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>

              <v-card-actions>
                <v-btn text href="/">ホーム画面に戻る</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- 購入失敗ダイアログ -->
          <v-dialog v-model="messageDialog" persistent width="300">
            <v-card class="mx-auto" max-width="344" outlined>
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-title class="headline mb-1">購入失敗</v-list-item-title>
                  <v-list-item-subtitle>{{ title }} の購入に失敗しました</v-list-item-subtitle>
                  <v-list-item-subtitle>{{ message }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>

              <v-card-actions>
                <v-btn text @click="messageDialog = false">閉じる</v-btn>
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
      thumbnail: "",
      goodPrice: 0,
      total: 0,
      userName: "",
      userBalance: "",
      message: "",
      cnts: [1, 2, 3, 4, 5, 6, 7, 8, 9],
      selectedCnt: 1,
      selectedCoupon: "",
      waitDialog: false,
      completeDialog: false,
      messageDialog: false,
      userId: -1,
      coupons: [],
      newBalance: 0,
      couponDescriptions: [],
      couponDetailDict: {}
    };
  },

  mounted() {
    axios.get("/api/goods/" + this.goodId).then(request => {
      this.title = request.data.title;
      this.description = request.data.desc;
      this.goodPrice = request.data.price;
      this.thumbnail = "/api/image/?file=" + request.data.thumbnail;
    });
    axios.get("/api/user").then(request => {
      this.userId = request.data.id;
      this.userName = request.data.name;
      this.userBalance = request.data.balance;
      this.coupons = request.data.coupons;
      request.data.couponDetails.forEach(coupon => {
        this.couponDetailDict[coupon.id] = {
          description: coupon.description,
          discount: coupon.discount
        };
        this.couponDescriptions.push({
          description: "[" + coupon.discount + "%off] " + coupon.description,
          id: coupon.id
        });
      });
      const noUse = "クーポンを使用しない";
      this.couponDescriptions.push({ description: noUse, id: -1 });
      this.selectedCoupon = -1;
      this.dryBuy();
    });
  },

  methods: {
    buy: function() {
      let discount = 0;
      if (this.selectedCoupon >= 0) {
        discount = this.couponDetailDict[this.selectedCoupon].discount;
      }
      this.waitDialog = true;
      axios
        .post("/api/buy", {
          id: this.userId,
          good_id: this.goodId,
          count: this.selectedCnt,
          discount: discount
        })
        .then(request => {
          if (request.data.status) {
            this.newBalance = request.data.balance;
            this.total = request.data.price;
            this.waitDialog = false;
            this.completeDialog = true;
          } else {
            this.message = request.data.message;
            this.waitDialog = false;
            this.messageDialog = true;
          }
        });
    },
    dryBuy: function() {
      this.total = "計算中...";
      let discount = 0;
      if (this.selectedCoupon >= 0) {
        discount = this.couponDetailDict[this.selectedCoupon].discount;
      }
      axios
        .get("/api/drybuy", {
          params: {
            good_id: this.goodId,
            count: this.selectedCnt,
            discount: discount
          }
        })
        .then(request => {
          this.total = request.data.price;
        });
    }
  },

  watch: {
    selectedCnt: function() {
      this.dryBuy();
    },
    selectedCoupon: function() {
      this.dryBuy();
    }
  }
};
</script>

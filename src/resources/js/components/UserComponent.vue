<template>
  <div class="container">
    <v-app id="inspire">
      <v-container>
        <appbar-component :icon-clicked="iconClicked" :icon-visible="!drawer" select-item="mypage" />

        <v-navigation-drawer clipped app v-model="drawer">
          <v-list dense nav class="py-5">
            <v-list-item-group v-model="selectItem" color="primary" mandatory>
              <v-list-item v-for="item in items" :key="item.title">
                <v-list-item-content>
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-navigation-drawer>
        <v-content class="mt-5">
          <!-- user information -->
          <v-row v-if="selectItem === 0">
            <v-col cols="8">
              <v-simple-table>
                <template v-slot:default>
                  <tbody>
                    <tr v-for="item in userInfos" :key="item.title">
                      <td>{{ item.title }}</td>
                      <td>{{ item.content }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-col>
            <v-col cols="4">
              <v-img v-if="userIcon" width="256px" :src="'/api/image?file=users/'+userIcon+'&x=512&y=512'"></v-img>
            </v-col>
          </v-row>
          <!-- user history -->
          <v-simple-table v-if="selectItem == 1">
            <template v-slot:default>
              <tbody>
                <tr>
                  <th>商品名</th>
                  <th>購入時間</th>
                  <th>個数</th>
                  <th>合計金額(割引後)</th>
                </tr>
                <tr v-for="item in history" :key="item.id">
                  <td>
                    <a :href="item.link">{{ item.title }}</a>
                  </td>
                  <td>{{ item.time }}</td>
                  <td>{{ item.count }}</td>
                  <td>{{ item.price }}</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-content>
      </v-container>
    </v-app>
  </div>
</template>

<style scoped>
.test {
  color: red;
}
</style>

<script>
export default {
  props: {
    reqId: String
  },
  data() {
    return {
      drawer: true,
      items: [],
      color: "primary",
      colors: ["primary", "blue", "success", "red", "teal"],
      right: true,
      miniVariant: false,
      expandOnHover: false,
      background: false,
      userInfos: [],
      userIcon: "",
      selectItem: 0,
      history: []
    };
  },
  mounted: function() {
    axios
      .get("/api/user", {
        params: {
          id: this.reqId
        }
      })
      .then(request => {
        this.items.push({ title: "ユーザー情報" });
        this.userInfos = [
          { title: "ユーザーID", content: request.data.id },
          { title: "表示名", content: request.data.name },
          { title: "Eメールアドレス", content: request.data.email },
          { title: "所持金", content: request.data.balance }
        ];
        this.userIcon = request.data.icon;
        axios.get("/api/history").then(request => {
          this.items.push({ title: "購入履歴" });
          this.history = request.data.history.map(function(x) {
            return {
              id: x.id,
              title: x.title,
              link: "/goods/" + x.good_id,
              time: x.updated_at,
              count: x.count,
              price: x.price
            };
          });
        });
      });
  },
  methods: {
    iconClicked: function() {
      this.drawer = true
    }
  }
};
</script>
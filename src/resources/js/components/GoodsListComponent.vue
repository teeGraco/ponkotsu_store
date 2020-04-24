    <template>
  <div class="container">
    <v-app>
      <appbar-component select-item="home" />
      <v-content>
        <v-row>
          <v-col cols="3">
            <v-select v-model="ord" :items="orders" label="値段順"></v-select>
          </v-col>
          <v-col cols="7">
            <v-text-field v-model="keyword" clearable type="text" @keyup.enter="search"></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-btn color="primary" @click="search">検索する</v-btn>
          </v-col>
        </v-row>
        <v-container fluid>
          <v-row wrap>
            <v-col
              v-for="(good, index) in goods"
              :key="index"
              cols="12"
              sm="12"
              md="6"
              lg="3"
              xl="2"
            >
              <v-hover v-slot:default="{ hover }">
                <v-card
                  width="320px"
                  outlined
                  class="mb-4"
                  :href="getGoodsUrl(good.id)"
                  :class="{ 'on-hover': hover }"
                  :elevation="hover ? 12 : 2"
                >
                  <v-img
                    :src="getImageUrl(good.thumbnail)"
                    class="white--text align-end"
                    gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                    height="200px"
                  ></v-img>
                  <v-card-title v-text="good.title"></v-card-title>

                  <v-card-subtitle>price: {{good.price}} pt.</v-card-subtitle>
                </v-card>
              </v-hover>
            </v-col>
          </v-row>
        </v-container>
        <!-- 検索中ダイアログ -->
        <v-dialog v-model="waitDialog" hide-overlay persistent width="300">
          <v-card color="primary" dark>
            <v-card-text>
              検索中
              <v-progress-linear indeterminate color="white" class="mb-0"></v-progress-linear>
            </v-card-text>
          </v-card>
        </v-dialog>
      </v-content>
    </v-app>
  </div>
</template>


<script>
export default {
  data: () => {
    return {
      keyword: "",
      ord: "asc",
      goods: [],
      orders: ["asc", "desc"],
      waitDialog: false,
    };
  },

  mounted() {
    axios.get("/api/goods").then(request => {
      this.goods = request.data.goods;
    });
  },

  methods: {
    getImageUrl: function(thumbnail) {
      return "/api/image?file=" + thumbnail;
    },
    getGoodsUrl: function(id) {
      return "/goods/" + id;
    },
    search: function() {
      this.waitDialog = true;
      let params = new URLSearchParams();
      if (this.keyword) params.append("keyword", this.keyword);
      // この辺でkeywordの記号をエスケープさせる。
      params.append("ord", this.ord);
      axios.post("/api/search", params).then(request => {
        this.goods = request.data.goods;
        this.waitDialog = false;
      }).catch(error => {
        this.waitDialog = false;
      });
    }
  }
};
</script>

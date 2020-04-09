<template>
  <div class="container">
    <v-app id="inspire">
      <appbar-component select-item="admin" />
      <v-container>
        <v-content class="mt-5">
          <!-- user information -->
          <v-simple-table>
            <template v-slot:default>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>name</th>
                  <th>Email</th>
                  <th>md5(password)</th>
                  <th>ユーザー作成日時</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.title">
                  <td>{{ user.id }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>{{ user.password }}</td>
                  <td>{{ user.created_at }}</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-content>
      </v-container>
    </v-app>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: []
    };
  },
  mounted: function() {
    axios
      .get("/api/user/all")
      .then(request => {
        this.users = request.data
      });
  },
  computed: {
    bg() {
      return this.background
        ? "https://cdn.vuetifyjs.com/images/backgrounds/bg-2.jpg"
        : undefined;
    }
  }
};
</script>
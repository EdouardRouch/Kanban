import { defineStore } from "pinia";

export const useUserStore = defineStore("user", {
  state: () => ({
    status: '',
    user: ''
  }),

  actions: {
    async signUp(username, password, password_verify) {
      const url = "http://" + window.location.hostname + ":8888";
      const res = await fetch(url + "/api/user/post.php", {
        method: "POST",
        headers: {
          'Content-Type': "application/json; charset=UTF-8",
        },
        credentials: 'include',
        body: JSON.stringify({
          username: username,
          password: password,
          password_verify: password_verify
        }),
      });

      if (!res.ok) { throw await res.json(); }

      this.user = username;
      return await res.json();
    },
    async logIn(username, password) {
      const url = "http://" + window.location.hostname + ":8888";
      const res = await fetch(url + "/api/user/login.php", {
        method: "POST",
        headers: {
          'Content-Type': "application/json; charset=UTF-8",
        },
        credentials: 'include',
        body: JSON.stringify({ username: username, password: password }),
      });

      if (!res.ok) { throw await res.json(); }

      this.user = username;
      return await res.json()
    },
    async logOut() {
      const url = "http://" + window.location.hostname + ":8888";
      const res = await fetch(url + "/api/user/logout.php", { credentials: 'include' });

      if (!res.ok) { throw await res.json(); }

      this.$reset();
      return await res.json();
    },
    async isLoggedIn() {
      const url = "http://" + window.location.hostname + ":8888";
      const res = await fetch(url + "/api/user/session_status.php", { credentials: 'include' });

      const json = await res.json();
      this.user = await json.records.user;
    }
  },
});

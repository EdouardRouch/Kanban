import { defineStore } from "pinia";

const API_URL = import.meta.env.VITE_API_URL;

export const useUserStore = defineStore("user", {
  state: () => ({
    status: '',
    user: ''
  }),
  actions: {
    async signUp(username: string, password: string, password_verify: string) {
      const res = await fetch(API_URL + "/api/user/post.php", {
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
    async logIn(username: string, password: string) {
      const res = await fetch(API_URL + "/api/user/login.php", {
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
      const res = await fetch(API_URL + "/api/user/logout.php", { credentials: 'include' });

      if (!res.ok) { throw await res.json(); }

      this.$reset();
      return await res.json();
    },
    async setSessionStatus() {
      const res = await fetch(API_URL + "/api/user/session_status.php", { credentials: 'include' });

      const json = await res.json();
      this.user = await json.records.user;
    }
  }
});

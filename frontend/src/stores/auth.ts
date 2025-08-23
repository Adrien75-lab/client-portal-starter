import { defineStore } from "pinia";

type Me = { authenticated: boolean; email?: string; filesCount?: number } | null;

export const useAuth = defineStore("auth", {
  state: () => ({ me: null as Me }),
  getters: {
    isAuth: (s) => !!s.me?.authenticated,
    email: (s) => s.me?.email ?? null,
  },
  actions: {
    async fetchMe() {
      const r = await fetch("/auth/me", {
        credentials: "include",
        cache: "no-store",
      });
      this.me = r.ok ? await r.json() : { authenticated: false };
    },
    async login(email: string, password: string) {
      const r = await fetch("/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ email, password }),
      });
      if (!r.ok) throw new Error("Unauthorized");
      await this.fetchMe();
    },
    async logout() {
      this.me = { authenticated: false };
      await fetch("/auth/logout", {
        method: "POST",
        credentials: "include",
        cache: "no-store",
      });
    },
  },
});

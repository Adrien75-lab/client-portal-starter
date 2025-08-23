<template>
  <v-app>
    <v-app-bar color="primary" density="comfortable">
      <v-app-bar-title>Espace client</v-app-bar-title>
      <v-spacer />
      <template v-if="auth.isAuth">
        <v-btn to="/contact" variant="text" prepend-icon="mdi-email">Contact</v-btn>
        <v-btn to="/files" variant="text" prepend-icon="mdi-folder">
          Mes Fichiers
          <v-badge v-if="auth.me?.filesCount" :content="auth.me.filesCount" color="error" overlap class="ml-2" />
        </v-btn>
        <v-btn @click="onLogout" variant="text" prepend-icon="mdi-logout">Déconnexion</v-btn>
      </template>
      <template v-else>
        <v-btn to="/login" variant="text" prepend-icon="mdi-login">Connexion</v-btn>
        <v-btn to="/register" variant="text" prepend-icon="mdi-account-plus">Inscription</v-btn>
        <v-btn to="/forgot" variant="text" prepend-icon="mdi-lock-reset">Mot de passe oublié</v-btn>
      </template>
    </v-app-bar>

    <v-main>
      <v-container class="py-6">
        <RouterView />
      </v-container>
    </v-main>

    <v-footer app class="text-caption">
      <v-container>© {{ new Date().getFullYear() }}</v-container>
    </v-footer>
  </v-app>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from './stores/auth'

const router = useRouter()
const auth = useAuth()

onMounted(() => { auth.fetchMe() })

async function onLogout() {
  await auth.logout()
  router.push('/login')   // push suffit, le guard va bloquer toute autre page
}
</script>
